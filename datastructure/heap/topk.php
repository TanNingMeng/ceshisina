<?php 

/**
 * 利用二叉堆算法来实现 TopN
实现流程是：
1、先读取10个或100个数到数组里面，这就是我们的topN数.
2、调用生成小顶堆函数，把这个数组生成一个小顶堆结构，这个时候堆顶一定是最小的.
3、从文件或者数组依次遍历剩余的所有数.
4、每遍历出来一个则跟堆顶的元素进行大小比较，如果小于堆顶元素则抛弃，如果大于堆顶元素则替换之.
5、跟堆顶元素替换完毕之后，在调用生成小顶堆函数继续生成小顶堆，因为需要再找出来一个最小的.
6、重复以上4~5步骤，这样当全部遍历完毕之后，我们这个小顶堆里面的就是最大的topN，因为我们的小顶堆永远都是排除最小的留下最大的，而且这个调整小顶堆速度也很快，只是相对调整下，只要保证根节点小于左右节点就可以.
7、算法复杂度的话按top10最坏的情况下，就是每遍历一个数，如果跟堆顶进行替换，需要调整10次的情况，也要比排序速度快，而且也不是把所有的内容全部读入内存，可以理解成就是一次线性遍历.
 */

function Heap(&$arr,$idx){
        $left  = ($idx << 1) + 1;
        $right = ($idx << 1) + 2;
    
        if (!$arr[$left]){
            return;
        }
    
        if($arr[$right] && $arr[$right] < $arr[$left]){
            $l = $right;
        }else{
            $l = $left;
        }
    
        if ($arr[$idx] > $arr[$l]){
             $tmp = $arr[$idx]; 
             $arr[$idx] = $arr[$l];
             $arr[$l] = $tmp;
             Heap($arr,$l);
        }
    }
    
    //这里为了保证跟上面一致，也构造500w不重复数
    /*
      当然这个数据集并不一定全放在内存，也可以在
      文件里面，因为我们并不是全部加载到内存去进
      行排序
    */
    for($i=0;$i<5000000;$i++){
        $numArr[] = $i;    
    }
    //打乱它们
    shuffle($numArr);
    
    //先取出10个到数组
    $topArr = array_slice($numArr,0,10);
    
    //获取最后一个有子节点的索引位置
    //因为在构造小顶堆的时候是从最后一个有左或右节点的位置
    //开始从下往上不断的进行移动构造（具体可看上面的图去理解）
    $idx = floor(count($topArr) / 2) - 1;
    
    //生成小顶堆
    for($i=$idx;$i>=0;$i--){
        Heap($topArr,$i);
    }
    
    var_dump(time());
    //这里可以看到，就是开始遍历剩下的所有元素
    for($i = count($topArr); $i < count($numArr); $i++){
        //每遍历一个则跟堆顶元素进行比较大小
        if ($numArr[$i] > $topArr[0]){
            //如果大于堆顶元素则替换
            $topArr[0] = $numArr[$i];
            /*
              重新调用生成小顶堆函数进行维护，只不过这次是从堆顶
              的索引位置开始自上往下进行维护，因为我们只是把堆顶
              的元素给替换掉了而其余的还是按照根节点小于左右节点
              的顺序摆放这也就是我们上面说的，只是相对调整下，并
              不是全部调整一遍
            */
            Heap($topArr,0);
        }
    }
    var_dump(time());