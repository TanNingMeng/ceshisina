# sort、uniq的常用用法举例

4 个月前

Linux或macOS中的sort、uniq可以对文本进行排序、去重。它们也可以通过管道跟其他文本工具配合使用。本文将介绍这两款工具的常用方法。

---

## **示例文件**

文件内容为某班学生成绩表，记录了四次测验的成绩。接下来对这个文件进行处理。处理过程中将不对原文件进行任何修改。

#### test.txt

    [root: ~]# cat test.txt
    No.    Name    Birth    T1    T2    T3    T4
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432326820506661    LM    1973/9/4    90    92    88    88    
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432326820506661    LM    1973/9/4    90    92    88    88
    

---

## **sort**

### **命令功能**

对指定的文本按要求进行排序。

### **命令用法**

    sort [选项] [文件]

若不指定输入文件，则从stdin输入。

**选项**

-t ：指定列分隔符，若不指定默认为空格。  
-k ：**指定排序的列，若不指定为第一列**。  
-r ：降序排列，若不指定为升序排列。  
-n ：按数字大小排列，若不指定将按字母大小排列。  
-u ：排列完成后去除重复行。

### **举例说明**

#### **1. 按学号由小到大进行排列**

    [root: ~]# sort -n test.txt
    

    No.    Name    Birth    T1    T2    T3    T4
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    434327830508554    MZZ    1983/4/7    60    68    75    69
    

#### **2. 按名字首字母由小到大进行排列**

基本操作

    [zenandidi: Desktop]$ sort -k 2 test.txt
    

    434327790706553    CDY    1978/9/8    72    79    80    90    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    No.    Name    Birth    T1    T2    T3    T4
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87
    

由于sort无法去除首行标题，我们必须配合sed工具使用。

    [root: ~]# sed '1d' test.txt | sort -k 2 | sed "1i`sed -n '1p' test.txt`"
    

    No.    Name    Birth    T1    T2    T3    T4
    434327790706553    CDY    1978/9/8    72    79    80    90    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87
    

#### **3. 按第三次测验的成绩由高到低进行排列**

基本操作

    [root: ~]# sort -n -k 6 test.txt
    

    No.    Name    Birth    T1    T2    T3    T4
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67
    

同样的，我们配合sed命令完善一下。

    [root: ~]# sed '1d' test.txt | sort -n -k 6 | sed "1i`sed -n '1p' test.txt`"
    

    No.    Name    Birth    T1    T2    T3    T4
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67
    

#### **4. 排列完成后，去除重复的行。**

    [root: ~]# sort -n -u test.txt

    No.    Name    Birth    T1    T2    T3    T4
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    434327830508554    MZZ    1983/4/7    60    68    75    69



---

## **uniq**

### **命令功能**

去除指定文本的连续重复行。

这个命令说实话在sort面前显得特别鸡肋，因为这个命令只能去除连续的重复行，对于非连续的重复行就无能为力了。而sort又包含了去除重复行的功能。所以这个命令我只用来看这个行重复了几次。

### **命令用法**

    uniq [选项] [文件]
    

若不指定输入文件，则从stdin输入。

**选项**

-d ：显示重复的行。  
-D ：显示所有重复的行。  
-u ：显示无重复的行。  
-c ：显示重复了几次（通常与-d配合使用）。  
-i ：不区分大小写。

### **举例说明**

#### **1. 去除重复的行**

我们先不用sort排序，看看结果。

    [root: ~]# uniq test.txt
    

    No.    Name    Birth    T1    T2    T3    T4
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432326820506661    LM    1973/9/4    90    92    88    88    
    434327830508554    MZZ    1983/4/7    60    68    75    69    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    432326820506661    LM    1973/9/4    90    92    88    88
    

可以看到，和原文件完全一样。而原文件里面的重复行都是非连续的。所以说，**uniq对非连续的重复行无能为力**。下面我们先用sort排一下序。

    [root: ~]# sort -n test.txt | uniq
    

    No.    Name    Birth    T1    T2    T3    T4
    432325760708563    HJR    1970/10/1    78    67    84    78    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432625790809542    GWW    1980/6/3    79    75    80    89    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327790706553    CDY    1978/9/8    72    79    80    90    
    434327830508554    MZZ    1983/4/7    60    68    75    69
    

这样就可以了。

#### **2. 显示重复次数（不重复的行不显示）**

    [root: ~]# sort -n test.txt | uniq -d -c
    

          2 432325770504342    LLJ    1978/3/5    96    89    97    67    
          2 432325781203496    CY    1976/12/2    89    86    80    79    
          3 432326820506661    LM    1973/9/4    90    92    88    88    
          2 434327750708554    ZX    1986/11/6    69    74    79    87
    

#### **3. 显示所有重复的行**

    [root: ~]# sort -n test.txt | uniq -D
    

    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325770504342    LLJ    1978/3/5    96    89    97    67    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432325781203496    CY    1976/12/2    89    86    80    79    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    432326820506661    LM    1973/9/4    90    92    88    88    
    434327750708554    ZX    1986/11/6    69    74    79    87    
    434327750708554    ZX    1986/11/6    69    74    79    87

[0]: https://www.zhihu.com/people/yuzenan888