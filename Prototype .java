package h2o;

public class DrawLots {

	public static void main(String ... args) {
		
		boolean isAvgMode = true;
		
		int index = 2;//起始楼层
		int total = 250;//总楼层/结束楼层
		int count = 15;//抽取总数
		
		if(count >=  total - index){
			//那还抽个J8？
			return;
		}
		
		
		//由于JDK随机数是闭-开区间，因此需要在总楼数上加1
		total += 1;
		
		String key = "#"; //+ args.hashCode() + System.currentTimeMillis();
		System.out.println(key);
		//此处使用CRC32进行散列（人懒方便），使用散列可以直接通过key获得一个散列长整数作为随机数种子(seed)
		java.util.zip.CRC32 crc = new java.util.zip.CRC32();
		crc.update(key.getBytes());
		//因为输入的key和散列是充分不必要关系，所以如果确保key的来源是随机的，则产生的seed也是随机的
		long seed = crc.getValue();
		System.out.println(seed);
		//每个seed所产生的随机序列是固定的，因此可以使用相同的seed来验证和复现该随机序列的真实有效的
		java.util.Random rand = new java.util.Random(seed);

		//是否使用平均区间算法
		if(isAvgMode){
			Integer[] result = new Integer[count];
			//计算每个区间的平均数量作为区间基准
			int section = (total - index) / count;
			//计算平均数量外的剩余数量
			int remainder = (total - index) % count;
			
			for(int round = 0; round < count; round++){
				//此处考虑到鼓励抢楼早的人所以将剩余数量从后面的区段往前逐个添加补齐
				//说人话：越早抢楼的中奖概率越高一丢丢
				if(round >= count - remainder){
					result[round] = index + rand.nextInt(section + 1);
					System.out.println(index +  "->" + (index + section + 1) + "=>" + result[round]);
					index += (section + 1);
				}else{
					result[round] = index + rand.nextInt(section);
					System.out.println(index +  "->" + (index + section) + "=>" + result[round]);
					index += section;
				}
			}
			System.out.println(java.util.Arrays.asList(result));
		}else{
			java.util.LinkedHashSet<Integer> result = new java.util.LinkedHashSet<Integer>();
			//简单暴力的直接抽取
			while(result.size() < count){
				for(int round = result.size(); round < count; round++){
					//由于index是起始位置，计算长度时需要补1
					result.add(index + rand.nextInt(total - index + 1));
				}
				//System.out.println(result);
			}
			System.out.println(result);
		}
		
	}

}
