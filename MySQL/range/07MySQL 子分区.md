# [MySQL 子分区][0]

### 介绍 

子分区其实是对每个分区表的每个分区进行再次分隔，目前只有RANGE和LIST分区的表可以再进行子分区，子分区只能是HASH或者KEY分区。子分区可以将原本的数据进行再次的分区划分。

### 一、创建子分区

子分区由两种创建方法，一种是不定义每个子分区子分区的名字和路径由分区决定，二是定义每个子分区的分区名和各自的路径

**1.不定义每个子分区**

```sql
    CREATE TABLE tb_sub (id INT, purchased DATE)
        PARTITION BY RANGE( YEAR(purchased) )
        SUBPARTITION BY HASH( TO_DAYS(purchased) )
        SUBPARTITIONS 2 (
            PARTITION p0 VALUES LESS THAN (1990),
            PARTITION p1 VALUES LESS THAN (2000),
            PARTITION p2 VALUES LESS THAN MAXVALUE
        );


    SELECT PARTITION_NAME,PARTITION_METHOD,PARTITION_EXPRESSION,PARTITION_DESCRIPTION,TABLE_ROWS,SUBPARTITION_NAME,SUBPARTITION_METHOD,SUBPARTITION_EXPRESSION 
    FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA=SCHEMA() AND TABLE_NAME='tb_sub';
```

![][1]

**2.定义每个子分区**

定义子分区可以为每个子分区定义具体的分区名和分区路径

```sql
    CREATE TABLE tb_sub_ev (id INT, purchased DATE)
        PARTITION BY RANGE( YEAR(purchased) )
        SUBPARTITION BY HASH( TO_DAYS(purchased) ) (
            PARTITION p0 VALUES LESS THAN (1990) (
                SUBPARTITION s0,
                SUBPARTITION s1
            ),
            PARTITION p1 VALUES LESS THAN (2000) (
                SUBPARTITION s2,
                SUBPARTITION s3
            ),
            PARTITION p2 VALUES LESS THAN MAXVALUE (
                SUBPARTITION s4,
                SUBPARTITION s5
            )
        );
```

![][2]

**3.测试数据**

    INSERT INTO tb_sub_ev() VALUES(1,'1989-01-01'),(2,'1989-03-19'),(3,'1989-04-19');

当往里面插入三条记录时，其中‘1989-01-01’和‘1989-04-19’存储在p0_s0分区中，‘1989-03-19’存储在p0_s1当中  

![][3]   

![][4]   

### 二、分区管理

分区管理和RANGE、LIST的分区管理是一样的

**1.合并分区**

将p0,p1两个分区合并

```sql
    ALTER TABLE tb_sub_ev REORGANIZE PARTITION p0,p1 INTO (
        PARTITION m1 VALUES LESS THAN (2000)
        ( SUBPARTITION n0,
          SUBPARTITION n1
         ) 
        
    );
```

![][5]

注意：合并分区的子分区也必须是两个，这点需要理解，因为必须和创建分区时每个分区只有两个子分区保持一致，合并分区不会造成数据的丢失。

**2.拆分分区**

```sql
    ALTER TABLE tb_sub_ev REORGANIZE PARTITION m1 INTO (
         PARTITION p0 VALUES LESS THAN (1990) (
                SUBPARTITION s0,
                SUBPARTITION s1
            ),
            PARTITION p1 VALUES LESS THAN (2000) (
                SUBPARTITION s2,
                SUBPARTITION s3
            )
        
    );
```

同样，拆分分区也必须保证每个分区是两个子分区。

**3.删除分区**

    ALTER TABLE  tb_sub_ev DROP PARTITION P0;

注意：由于分区是RANGE和LIST分区，所以删除分区也是同RANGE和LIST分区一样，这里只能对每个分区进行删除，不能针对每个子分区进行删除操作，删除分区后子分区连同数据一并被删除。

### 三、错误的子分区创建

**1.要不不定义各个子分区要不就每个都需要定义**

```sql
     CREATE TABLE tb_sub_ev_nex (id INT, purchased DATE)
        PARTITION BY RANGE( YEAR(purchased) )
        SUBPARTITION BY HASH( TO_DAYS(purchased) ) (
            PARTITION p0 VALUES LESS THAN (1990) (
                SUBPARTITION s0,
                SUBPARTITION s1
            ),
            PARTITION p1 VALUES LESS THAN (2000),
            PARTITION p2 VALUES LESS THAN MAXVALUE (
                SUBPARTITION s4,
                SUBPARTITION s5
            )
        );  
```

这里由于分区p1没有定义子分区，所以创建分区失败

### 四、移除表的分区

    ALTER TABLE tablename
    REMOVE PARTITIONING ;

注意：使用remove移除分区是仅仅移除分区的定义，并不会删除数据和drop PARTITION不一样，后者会连同数据一起删除

**参考：**



### **总结** 

子分区的好处是可以对分区的数据进行再分，这样数据就更加的分散，同时还可以对每个子分区定义各自的存储路径，这部分内容在指定各分区路径的下一篇文章中单独进行讲解。

[0]: http://www.cnblogs.com/chenmh/p/5649447.html
[1]: ./img/135426-20160707115234811-1175668034.png
[2]: ./img/135426-20160707115250264-1652282900.png
[3]: ./img/135426-20160707115258296-1013162340.png
[4]: ./img/135426-20160707115329530-559125068.png
[5]: ./img/135426-20160707115339749-1381999461.png
