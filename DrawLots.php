<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		//Test Area\\
		$isAvgMode = true;
		
		$index = 2;//起始楼层
		$total = 500;//总楼层/结束楼层
		$count = 15;//抽取总数
		//Test Area//
		
		$index = intval($_POST["index"]);
		$total = intval($_POST["total"]);
		$count = intval($_POST["count"]);
		$isAvgMode = intval($_POST["mode"]) == 1 ? true : false;
		$dump = intval($_POST["show"]) == 1 ? true : false;
		
		$msg = "";
		$ret = "";
		
		
		if($count <= 0 || $total - $index <= 0  || $count >=  $total - $index){
			$ret = "那你这还抽个J8？";
			$pass = false;
		}else{
                        $pass = true;
                }

       if($pass){
		
		//由于后续我们要改变为闭-开区间，因此需要在总楼数上加1
		$total += 1;
		
		$key = $_POST["key"];
		//此处使用CRC32进行散列（可以用MD5或者SHA），使用散列可以直接通过key获得一个散列长整数作为随机数种子(seed)
		//因为输入的key和散列是充分不必要关系，所以如果确保key的来源是随机的，则产生的seed也是随机的
		$seed = crc32($key);
		//每个seed所产生的随机序列是固定的，因此可以使用相同的seed来验证和复现该随机序列的真实有效的
		//这里将使用seed初始化随机函数
		srand($seed);

		//是否使用平均区间算法
		if($isAvgMode){
			$result = array();
			//计算每个区间的平均数量作为区间基准
			$section = intval(($total - $index) / $count);
			//计算平均数量外的剩余数量
			$remainder = ($total - $index) % $count;
			
			for($round = 0; $round < $count; $round++){
				//此处考虑到鼓励抢楼早的人所以将剩余数量从后面的区段往前逐个添加补齐
				//说人话：越早抢楼的中奖概率越高一丢丢
				//PHP的随机数是闭-闭区间，所以我们要在后面的边界-1
				if($round >= $count - $remainder){
					$result[$round] = $index + rand(0, $section);
					$msg .= $index .  "->" . ($index + $section) . "=>" . $result[$round] . "\n";
					$index += $section + 1;
				}else{
					$result[$round] = $index + rand(0, $section - 1);
					$msg .= $index .  "->" . ($index + $section - 1) . "=>" . $result[$round] . "\n";
					$index += $section;
				}
			}
			$ret = implode(",",$result);
		}else{
			$result = array();
			//简单暴力的直接抽取
			while(sizeof($result) < $count){
				for($round = sizeof($result); $round < $count; $round++){
					$result[] = $index + rand(0, $total - $index);
				}
				$msg .= implode(",",$result) . "-=>";
				$result = array_unique($result, SORT_NUMERIC);
				$msg .= implode(",",$result) . "\n";
			}
			$ret = implode(",",$result);
		}
          }
}	
?>
<html>
<head>
</head>
<body>
<form method="POST">
  <fieldset>
    <legend>抽签生成器</legend>
      口号：<br />
<textarea name="key" rows="5" cols="50">你可以塞进你的URL、你的宣传语，等等。如需要确保某一次抽取的随机性，请确保口号(明文)中添加一点随机因素，如股市收市结果，或者某一时刻的汇率。你的口号和下面的参数将组成一个唯一的抽奖结果，任何人使用相同的输入都将得到一致的结果，以便复现验证，因此这里不会保存任何信息。</textarea><br />
      起始位(含)：<input type="text" name="index" value="2" /><br />
      截止位(含)：<input type="text" name="total" /><br />
	  抽取数量：<input type="text" name="count" /><br />
      抽签模式：<input type="radio" name="mode" value="1" checked>区间平均</input><input type="radio" name="mode" value="0">全局抽取</input><br />
	  抽签过程：<input type="checkbox" name="show" value="1">显示</input><br />
	  <input type="submit" value="手气不错"/>
  </fieldset>
  <fieldset>
  <legend>抽签结果</legend>
      <pre><?php echo $ret; ?></pre>
  </fieldset>
  <fieldset>
  <legend>抽签过程</legend>
      <pre><?php if($dump) echo $msg; ?></pre>
  </fieldset>
</form>
<img src="why.png" />
</body>