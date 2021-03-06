 **fsck命令** **-->用来检查并修复Linux文件系统**

 【**适用**】

 1) 文件系统：ext2 ext3 reiserfs xfs等  
2) 范围：提示文件系统需要FSCK时，未执行或FSCK执行完成

 【 **症状** 】

 1) 无法MOUNT分区;  
2) 大量文件、目录丢失，根目录下生成/LOST+FOUND文件夹，里面有大量#XXXXXX类的文件和目录;  
3) fsck很快报错完成;  
4) fsck执行时，有大量提示，如修改节点、清0节点等操作

 

![][0]

 【 **应急** 】

 1、如遇提示FSCK时，请小心。如果可能，请尽快断开系统，UMOUNT所有分区。  
2、必需执行FSCK时，先做准备工作，方法一：可事先用dd命令将所涉及到的分区输出到另外的存储体上（最好不要在出错的存储体本身上做dd） 命令大致结构可如： dd if=/dev/sda0 of=/dev/sdb0 .....  
3、如上面几种方式均因条件等原因无法实施，必须执行时，可小心观察FSCK的执行提示（关掉-a）如果发现有提示节点错误需更正或清0、节点描述文件大小不正确等信息，应停止执行FSCK

 【 **备注** 】

 1) 如果可能，先对故障区域做dd全镜像后再执行，或者以只读方式执行，并仔细看修复过程，如果提示大量inode错误、需要重建树、或大小不对等就不可再继续下去了

 2) 文件系统常见错误,并且问题通常原因是电源失败、硬件失败、或操作错误，例如没有正常关闭系统

 3) fsck 只能运行于未mount的文件系统，不要用于已mount的文件系统

 4) 修复完成后，会出现提示“FILE SYSTEM WAS MODIFIED”。这时输入命令 "reboot" 命令重启系统

[0]: ./img/20170301060427544.png