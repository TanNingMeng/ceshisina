<?php
/**
 * 将一个罗马数转化成对应的阿拉伯数

 * 阿拉伯数，顾名思义 ，就是我们平时使用最多的数，比如，1,2,3,4，.....
罗马数，是数字最早的表示方式。基本的字符有：I、V、X、L、C、D、M，对应的数字
分别是：1,5,10,50,100,500,1000。
下边给出罗马数的计数规律：
  1.两个相同的字符挨着写，表示相加。比如：XX转化成阿拉伯数就是20.
  2.如果一个字符表示的数比左边的字符表示的数大，则转化成阿拉伯数就是这个数减去
左边的数。
比如：IX表示的阿拉伯数就是10-1 = 9.
          ICMI表示的阿拉伯数是：1000-（IC）+I = 1000-（100-1）+1 = 902.
转化的时候必须先找出给出的罗马数中的最大的字符。
 */