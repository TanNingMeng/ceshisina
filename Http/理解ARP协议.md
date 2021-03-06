# 理解ARP协议

卡巴拉的树 关注 2017.02.25 18:45  字数 2231  

我们知道在网络中通讯，都是知道对方的IP地址后，才能发起连接，IP地址所在的层是网络层，而在网络层下面是数据链路层，这里IP数据包继续被封装成以太网数据帧，当然还有别的数据链路层格式，但是数据链路层也需要寻址机制，常常就是48bit的硬件地址,又叫MAC地址。

![ARP][1]
  
ARP协议就是起到在IP地址到对应的硬件地址之间提供映射作用的，所以ARP协议又叫**地址解析协议**。

## 基本工作流

任何时候，当我们发起一个网络接连时，无非有以下的过程：

1. 知道目标主机名，通过gethostname函数将主机名转换为IP地址，这个函数在DNS（域名系统）中称为解析器，这里推荐看一下阮一峰老师的博文：[DNS原理入门][2]。
1. 应用程序通过TCP或者UDP使用得到的IP建立连接。
1. 如果目标主机在本地网络上，知道IP地址，我们就可以找到对应的主机，如果目标主机在远程网络中，系统就会找位于本地网络的下一站路由地址，通过IP选路让路由器转发IP数据报，这些操作属于IP协议的核心，在此略过。
1. 当主机知道IP地址，并把数据报发过去的过程之前，主机在链路层实际上是要知道目标主机的物理地址的，每台主机的物理地址又称MAC地址是全球唯一的。在这一步，就需要我们的ARP协议。
1. ARP协议发送一个ARP请求，这个ARP请求是一个广播数据帧，意味着局域网内，每一台机器都能收到，ARP数据帧中包含着目的主机的IP地址，因为是广播发送，如果哪台主机拥有这个IP，则会反馈消息，发回自己的硬件地址。
1. 得到了目的主机的硬件地址后，就包含着IP数据报的以太网数据帧就可以正常发送了。

## ARP的分组格式

在以太网中，ARP请求和应答都有着下面的分组格式。

![分组格式][3]

**以太网的目的地址和源地址**：目的以太网地址全为1，即FF:FF:FF:FF:FF:FF则为广播地址，在本地局域网内，所有的以太网接口都要接收这个数据帧。

**帧类型**：2个字节长的帧类型，告诉我们这个以太网数据帧携带的是什么数据。对于ARP来说这两个字节是0x0806，对于IP数据报是0x0800，还有RARP（逆地址解析协议）是0x8035。

**硬件类型和协议类型**：这两个字段就是用来描述ARP分组的，硬件类型用来指代需要什么样的物理地址，协议类型则是需要映射的协议地址类型。用来描述ARP时，表达的就是我有协议类型IP，我需要的硬件类型是以太网的物理地址。那么硬件地址就为1，表示以太网地址，协议类型是0x0800，看这边与前面的帧类型中提到的IP数据报的帧类型是一样的，这是有意设计的。

**硬件地址长度和协议地址长度**：这里的值分别为6字节和4字节。代表48bit的以太网地址，32bit的IP地址。

**操作码**：1=>ARP请求, 2=>ARP应答，3=>RARP请求，4=>RARP应答。这些值用于区分具体操作类型，因为字段都相同，所以必须指明操作码，不然连请求还是应答都分不清。

**最后四个字段**：

* 源硬件地址
* 源协议地址
* 目标硬件地址
* 目标协议地址。  
**注意到这里有两个字段是与分组首端重复的。**我们在发送ARP请求时，只有**目标硬件地址**是空着的，因为我们请求的就是它的值，当对应机器收到后，就会把自己的硬件地址写到这个字段，并把操作码改成2，再发回去。于是就知道彼此的硬件地址，开始真正的通讯。

## ARP高速缓存

知道了ARP发送的原理后，我们不禁疑惑，如果每次发之前都要发送ARP请求硬件地址会不会太慢，但是实际上ARP的运行是非常高效的。那是因为每一个主机上都有一个ARP高速缓存，我们可以在命令行键入arp -a获取本机ARP高速缓存的所有内容：

![arp -a][4]
  
高速缓存中的每一项的生存时间一般为20分钟，有了这些缓存，我们经常直接使用硬件地址，加快速度了。

**以上说的都是在一个本地网络内完成的，如果ARP请求是从一个网络主机发送到另一个网络主机呢？下面介绍ARP代理的概念**

## ARP代理

如果ARP请求是从一个网络主机发送到另一个网络主机，那么连接这两个主机的路由器就可以回答该请求，这个过程称为委托ARP或者ARP代理。我们知道IP路由选择，如果主机不相连，我们就把数据报发送到一默认路由上，由路由器来转发该数据报。在ARP协议中，我们发往网络的请求主机物理地址也会由路由器回答，得到的就是路由器的物理地址，发送方就根据这个物理地址把数据报发送到路由器，由路由器转发，再下面的事情由路由器完成，那是属于IP协议的事了，当然在那个过程中，也不断使用ARP协议获取每一步的物理地址。

## 总结

#### 目标IP与自己在同一网段

* **arp高速缓存有目标IP的MAC地址**：直接发送到该物理地址
* **arp高速缓存没有目标IP的MAC地址**：发送ARP广播请求目标IP的MAC地址，缓存该MAC地址，然后发数据报到该MAC地址。

#### 目标IP与自己不在同一个网段

**这种情况需要将包发给默认网关，所以主要获取网关的MAC地址**

* **arp高速缓存有默认网关的MAC地址**：直接发送IP数据报道默认网关，再由网关转发到外网。
* **arp高速缓存没有默认网关的MAC地址** ：还是发送ARP广播请求默认网关的MAC地址，缓存该地址，并且发送数据报到网关。

## 另外一个话题：ARP欺骗

**ARP欺骗**又叫**ARP毒化**，英文ARP spoofing，是一种针对ARP的攻击方式，这里简略介绍一下。  
**运行机制**  
ARP欺骗主要是攻击者发送大量假的ARP数据包到网络上，尤其是网关上。假设你的网关的IP地址是192.168.0.2,MAC地址为00-11-22-33-44-55,你发送的数据都会从这个MAC地址经过，这时候我发送大量ARP数据包，然而我的包是构造出来的，IP是你的IP，但是MAC地址我替换成了我的MAC地址，这时候你更新你的ARP缓存时，就会把我机器的MAC地址当成192.168.0.2的MAC地址，于是你的流量都到我这来了，我可以把你的数据改改再发给网关，或者什么都不做，你都上不了网了。  
**防治方法**  
最理想的用静态ARP，不过在大型网络不行，因为ARP经常需要更新，另外一种方法，例如DHCP snooping，网络设备可借由DHCP保留网络上各电脑的MAC地址，在伪造的ARP数据包发出时即可侦测到。此方式已在一些厂牌的网络设备产品所支持。

参考资料：

* TCP/IP详解 (卷一：协议)
* 维基百科：[Address Resolution Protocol(ARP)][5]


[1]: http://upload-images.jianshu.io/upload_images/272719-34671b71bb13c50a.PNG
[2]: http://www.ruanyifeng.com/blog/2016/06/dns.html
[3]: http://upload-images.jianshu.io/upload_images/272719-fd151846ee54d559.PNG
[4]: http://upload-images.jianshu.io/upload_images/272719-e5ffd614d124d4a3.PNG
[5]: https://en.wikipedia.org/wiki/Address_Resolution_Protocol