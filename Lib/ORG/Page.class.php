<?php
/******************************
'类名：Pager
'模板标签说明：
{N1}：首页
{N2}：上一页
{N3}：下一页
{N4}：尾页
{N5}：当前页码
{N6}：页码总数
{N7}：每页条数
{N8}：文章总数
{N9}：上十页
{N10}：下十页
'简单的循环标签：
{L}：   循环标签开始
{N}：  循环内单标签：页码
{/L}：  循环标签结束
'复杂的循环标签：
{B}：   循环标签开始
<a href="{U}">{N}</a>|||{N} :带超连接的数字页码
{U}:对应的页码地址
{N}:页码
{/B}：  循环标签结束
实例：
$p=new Pager($_GET["page"],2,21,'search.php?page=[page]',5,5);
echo $p->showStyle(2);
'******************************/
class Page{
	var $currentPage;//当前页
	var $pageSize;//每页显示条数
	var $recordCount;//数据库里的总记录数
	var $totalPage;//总页数
	/*
	$pageUrl,如果为空,则在当前地址(包括后面的参数)加上"&page="+页数;
	如果不为空,则直接加上页数
	*/
	var $pageUrl;//后面的参数部分 如:pageUrl="?page=",
	var $leftOffSet;//左偏移量,用于数字分页
	var $rightOffSet;//右偏移量,用于数字分页和左偏移量一起使用
	var $ajaxfuc;  //ajax分页函数名
	var $template;//分页模板
	var $tpl;//存放解析后的分页代码
	var $L;//语言包
	/*
	*方法：void Pager($_currentpage=1,$_pagesize=10,$_recordcount=0,$_pageurl="?page=",)
	*作用：构造函数,类初始化
	*说明：无
	*例子：无
	*/
	function Page(
	$_currentpage=1,
	$_pagesize=10,
	$_recordcount=0,
	$_pageurl="",
	$_leftoffset=4,
	$_rightoffset=5,
	$_ajaxfunc=""
	){
		//当前页
		$this->currentPage=ceil(abs(@$_currentpage+0));
		(empty($this->currentPage))?$this->currentPage=1:'';
		//每页显示条数
		$this->pageSize=ceil(abs(@$_pagesize+0));
		(empty($this->pageSize))?$this->pageSize=5:'';
		//总记录数
		$this->recordCount=ceil(abs(@$_recordcount+0));
		(empty($this->recordCount))?$this->recordCount=0:'';
		//url
		$this->pageUrl=$_pageurl;
		(empty($this->pageUrl))?$this->pageUrl=$_SERVER['PHP_SELF']."?page=":'';
		//左偏移
		$this->leftOffSet=ceil(abs(@$_leftoffset+0));
		(empty($this->leftOffSet))?$this->leftOffSet=4:'';
		//右偏移
		$this->rightOffSet=ceil(abs(@$_rightoffset+0));
		(empty($this->rightOffSet))?$this->rightOffSet=5:'';
		//ajax函数名
		$this->ajaxfunc=$_ajaxfunc;
		//重新计算所有数据，以得到准确的数据
		$this->totalPage=ceil($this->recordCount / $this->pageSize);//计算总页数
		//判断当前页是否大于总页数
		if($this->currentPage > $this->totalPage){$this->currentPage=$this->totalPage;}
		//防止当$this->totalPage=0时，$this->currentPage也等于0
		if($this->currentPage <=0){$this->currentPage=1;}
		//初始化语言包
		$this->L=array(
		"N1"=>"首页",
		"N2"=>"上一页",
		"N3"=>"下一页",
		"N4"=>"尾页",
		"N9"=>"上十页",
		"N10"=>"下十页"
		);
	}
	/*
	*方法：string parseTemplate()
	*作用：解析模板，得到分页风格
	*说明：单标签不检查直接替换，循环标签要检查存在后才替换，提高效率
	*例子：无
	*/
	function parseTemplate(){
		$this->tpl=$this->template;
		if($this->template=="")return;
		$a="";
		/*首页{N1} 上一页{N2} 下一页{N3} 尾页{N4} 解析开始*/
		if($this->totalPage>1){
			if($this->currentPage > 1){
				//首页
				$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]","1",$this->pageUrl)."\" class=\"page_left_1\">".$this->L['N1']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(1)"."\" class=\"page_left_1\">".$this->L['N1']."</a>";
				$this->tpl=str_replace("{N1}",$a,$this->tpl);
				//前一页
				$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",($this->currentPage-1),$this->pageUrl)."\" class=\"page_left_2\">".$this->L['N2']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".($this->currentPage-1).")"."\" class=\"page_left_2\">".$this->L['N2']."</a>";
				$this->tpl=str_replace("{N2}",$a,$this->tpl);
				if($this->currentPage<$this->totalPage){
					//下一页
					$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",($this->currentPage+1),$this->pageUrl)."\" class=\"page_right_2\">".$this->L['N3']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".($this->currentPage+1).")"."\" class=\"page_right_2\">".$this->L['N3']."</a>";
					$this->tpl=str_replace("{N3}",$a,$this->tpl);
					//尾页
					$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",$this->totalPage,$this->pageUrl)."\" class=\"page_right_1\">".$this->L['N4']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".$this->totalPage.")"."\" class=\"page_right_1\">".$this->L['N4']."</a>";
					$this->tpl=str_replace("{N4}",$a,$this->tpl);
				}else{
					//下一页
					$this->tpl=str_replace("{N3}",'<span class="page_right_2_2">'.$this->L['N3'].'</span>',$this->tpl);
					//尾页
					$this->tpl=str_replace("{N4}",'<span class="page_right_1_1">'.$this->L['N4'].'</span>',$this->tpl);
				}
			}else{
				//首页
				$this->tpl=str_replace("{N1}",'<span class="page_left_1_1">'.$this->L['N1'].'</span>',$this->tpl);
				//前一页
				$this->tpl=str_replace("{N2}",'<span class="page_left_2_2">'.$this->L['N2'].'</span>',$this->tpl);
				//下一页
				$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",($this->currentPage+1),$this->pageUrl)."\" class=\"page_right_2\">".$this->L['N3']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".($this->currentPage+1).")"."\" class=\"page_right_2\">".$this->L['N3']."</a>";
				$this->tpl=str_replace("{N3}",$a,$this->tpl);
				//尾页
				$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",$this->totalPage,$this->pageUrl)."\" class=\"page_right_1\">".$this->L['N4']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".$this->totalPage.")"."\" class=\"page_right_1\">".$this->L['N4']."</a>";
				$this->tpl=str_replace("{N4}",$a,$this->tpl);
			}
		}else{
			//解析 首页，尾页,前一页,后一页
			$this->tpl=str_replace("{N1}",'<span class="page_left_1_1">'.$this->L['N1'].'</span>',$this->tpl);
			$this->tpl=str_replace("{N2}",'<span class="page_left_2_2">'.$this->L['N2'].'</span>',$this->tpl);
			$this->tpl=str_replace("{N3}",'<span class="page_right_2_2">'.$this->L['N3'].'</span>',$this->tpl);
			$this->tpl=str_replace("{N4}",'<span class="page_right_1_1">'.$this->L['N4'].'</span>',$this->tpl);
		}
		//解析 当前页码{N5} 页码总数{N6} 每页条数{N7} 文章总数{N8}
		$this->tpl=str_replace("{N5}",'<span class="disabled">'.$this->currentPage.'</span>',$this->tpl);
		$this->tpl=str_replace("{N6}",'<span class="disabled">'.$this->totalPage.'</span>',$this->tpl);
		$this->tpl=str_replace("{N7}",'<span class="disabled">'.$this->pageSize.'</span>',$this->tpl);
		$this->tpl=str_replace("{N8}",'<span class="disabled">'.$this->recordCount.'</span>',$this->tpl);
		/*首页{N1} 上一页{N2} 下一页{N3} 尾页{N4} 解析结束*/
		/*上十页{N9}，下十页{N10} 解析开始*/
		if($this->currentPage-10>=1){
			//上十页
			$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",($this->currentPage-10),$this->pageUrl)."\" >".$this->L['N9']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".($this->currentPage-10).")"."\" >".$this->L['N9']."</a>";
			$this->tpl=str_replace("{N9}",$a,$this->tpl);
		}else{
			$this->tpl=str_replace("{N9}",'<span class="disabled">'.$this->L['N9'].'</span>',$this->tpl);
		}
		if($this->currentPage+10<=$this->totalPage){
			//下十页
			$a=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",($this->currentPage+10),$this->pageUrl)."\" >".$this->L['N10']."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".($this->currentPage+10).")"."\" >".$this->L['N10']."</a>";
			$this->tpl=str_replace("{N10}",$a,$this->tpl);
		}else{
			$this->tpl=str_replace("{N10}",'<span class="disabled">'.$this->L['N10'].'</span>',$this->tpl);
		}
		//解析数字列表
		//if($this->getTpl("L") || $this->getTpl("B")){$this->parseNumList();}
	}
	/*
	*方法：parseNumList()
	*作用：解析数字列表
	*说明：无
	*例子：无
	*/
	function parseNumList(){
		$firstnum;$lastnum;
		$M;$A;$L1;$B1;
		//计算左偏移,右偏移
		if($this->currentPage-$this->leftOffSet<1){
			$firstnum=1;
		}else{$firstnum=$this->currentPage-$this->leftOffSet;}
		if($this->currentPage+$this->rightOffSet>$this->totalPage){
			$lastnum=$this->totalPage;
		}else{$lastnum=$this->currentPage+$this->rightOffSet;}
		if($lastnum<1){$lastnum=1;}

		/*简单标签（数字列表）{L}{N}{/L} 解析开始*/
		if($M=$this->getTpl("L")){
			for($i=$firstnum;$i<=$lastnum;$i++){
				if($i==$this->currentPage){
					$L1.=str_replace("{N}",'<span class="page_now">'.$i.'</span>',$M[1]);
				}else{
					$A=empty($this->ajaxfunc)?"<a href=\"".str_replace("[page]",$i,$this->pageUrl)."\" >".$i."</a>":"<a href=\"javascript:".$this->ajaxfunc."(".$i.")"."\" >".$i."</a>";
					$L1.=str_replace("{N}",$A,$M[1]);
				}
			}
			$this->tpl=str_replace($M[0],$L1,$this->tpl);
		}
		/*简单标签（数字列表）{L}{N}{/L} 解析结束*/
		/*复杂标签（数字列表）{B}{#<a href="{U}">{N}</a>|||{N}#}{/B} 解析开始*/
		if($M=$this->getTpl("B")){
			for($i=$firstnum;$i<=$lastnum;$i++){
				if($i==$this->currentPage){
					$A=empty($this->ajaxfunc)?str_replace("[page]",'<span class="page_now">'.$i.'</span>',$this->pageUrl):"javascript:".$this->ajaxfunc."(".$i.")";
					$B2=str_replace("{U}",$A,$M[2]);
					$B2=str_replace("{N}",'<span class="page_now">'.$i.'</span>',$B2);
					$B1.=$B2;
				}else{
					$A=empty($this->ajaxfunc)?str_replace("[page]",$i,$this->pageUrl):"javascript:".$this->ajaxfunc."(".$i.")";
					$B2=str_replace("{U}",$A,$M[1]);
					$B2=str_replace("{N}",$i,$B2);
					$B1.=$B2;
				}
			}
			$this->tpl=str_replace($M[0],$B1,$this->tpl);
		}
		/*复杂标签（数字列表）{B}{#<a href="{U}">{N}</a>|||{N}#}{/B} 解析结束*/

	}
	/*
	*方法：string getTemplate($t)
	*作用：获取分页用模板
	*说明：无
	*例子：无
	*/
	function getTemplate($t){
		$this->template=$t;
		$this->tpl=$this->template;
	}

	/*
	*方法：array getTpl($tag)
	*作用：获取循环标签模板,返回匹配的数组
	*说明：$tag有两个值:"L","B" 分别获取对应的两种循环标签
	*例子：getTpl("L")
	*/
	function getTpl($tag){
		$T="";$P;$M;
		if($tag=="L"){
			$P="/\{L\}([\w\W]*?{N}[\w\W]*?){\/L}/";
			$T=(@preg_match($P,$this->template,$M))?$M:"";
		}
		if($tag=="B"){
			$P="/\{B\}([\w\W]*?)\|\|\|([\w\W]*?)\{\/B\}/";
			$T=(@preg_match($P,$this->template,$M))?$M:"";
		}
		return $T;
	}
	/*
	*方法：void resetLang($l)
	*作用：重新设定语言包
	*说明：无
	*例子：无
	*/
	function resetLang($l){
		if(is_array($l)){$this->L=$l;}else{$this->showInfo("重新设定语言包出错:Pager->resetLang()");}
	}
	/*
	*方法：void setLimit()
	*作用：构造sql里的LIMIT N,M语句
	*说明：无
	*例子："SELECT * FROM TABLE  ".Pager->setLimit()
	*/
	function setLimit(){
		$limit=" LIMIT ".($this->currentPage-1)*$this->pageSize;
		$limit.=",$this->pageSize";
		return $limit;
	}
	/*
	*方法：bool isInt($str)
	*作用：检验是否是正整数
	*说明：在类内部使用
	*例子：isInt("12")
	*/
	function isInt($str){
		return @preg_match("/^[0-9]+$/",$str);
	}
	/*
	*方法：void showInfo($info="")
	*作用：显示内部类种信息(包括错误信息)
	*说明：无
	*例子：showInfo("分页出错")
	*/
	function showInfo($info=""){
		$cssstyle="style=\"";
		$cssstyle.="font:bold 12px 150%,'Arial';border:1px solid #CC3366;";
		$cssstyle.="width:50%;color:#990066;padding:2px;\"";
		$str="\n<ul ".$cssstyle."><li>".$info."</li></ul>\n";
		echo $str;
	}
	/*基本设置结束*/
	/**********设置默认风格开始**********/
	/*
	*方法：void showStyle($t)
	*作用：显示默认风格
	*说明：无
	*例子：showStyle(1)//显示第一种风格
	*/
	function showStyle($t=1){
		switch($t){
			case 1:return $this->style_1();break;
			case 2:return $this->style_2();break;
			case 3:return $this->style_3();break;
			case 4:return $this->style_4();break;
			case 5:return $this->style_5();break;
			case 6:return $this->style_6();break;
			default:return $this->style_1();break;
		}
	}
	/*
	*方法：string Style_1(){}
	*作用：返回第一种风格
	*说明：无
	*例子：首页 上一页 下一页 尾页 || 共:84条记录 5页 当前为第1页 每页20条
	*/
	function Style_1(){
		$t="{N1} {N2} {N3} {N4} || 共:{N8}条记录 {N6}页 当前为第{N5}页 每页{N7}条";
		$this->getTemplate($t);
		$this->parseTemplate();
		return $this->tpl;
	}
	/*
	*方法：string Style_2(){}
	*作用：返回第一种风格
	*说明：无
	*例子：首页 |< |<<[1] [2] [3] [4] [5] [6] [7] [8] [9] >>| >| 尾页
	*/
	function Style_2(){
		$t="{N1} {N2} {N9} {L} {N} {/L} {N10} {N3} {N4}";
		//重设语言包
		$this->L=array(
		"N1"=>"首页",
		"N2"=>"|<",
		"N3"=>">|",
		"N4"=>"尾页",
		"N9"=>"|<<",
		"N10"=>">>|"
		);
		$this->getTemplate($t);
		$this->parseTemplate();
		$this->parseNumList();
		return $this->tpl;
	}
	function Style_3(){
		$t="{N1} {N2} {B} <a href=\"{U}\">{N}</a> ||| {N}{/B} {N3} {N4}";
		//重设语言包
		$this->L=array(
		"N1"=>"首页",
		"N2"=>"上一页",
		"N3"=>"下一页",
		"N4"=>"尾页"
		);
		$this->getTemplate($t);
		$this->parseTemplate();
		$this->parseNumList();
		return $this->tpl;
	}
	function Style_4(){
		$t="{N1} {N9} {N2} {B} <a href=\"{U}\">{N}</a> ||| {N}{/B} {N3} {N10} {N4}";
		//重设语言包
		$this->L=array(
		"N1"=>"首页",
		"N2"=>"上一页",
		"N3"=>"下一页",
		"N4"=>"尾页",
		"N9"=>"上十页",
		"N10"=>"下十页"
		);
		$this->getTemplate($t);
		$this->parseTemplate();
		$this->parseNumList();
		return $this->tpl;
	}
	function Style_5(){
		$t="<a href='#'>{N2}</a> {B} <a href=\"{U}\">[{N}]</a> ||| [{N}]{/B} <a href='#'>{N3}</a>";
		//重设语言包
		$this->L=array(
		"N2"=>'<img src="images/page_left.gif"/>',
		"N3"=>'<img src="images/page_right.gif"/>'
		);
		$this->getTemplate($t);
		$this->parseTemplate();
		$this->parseNumList();
		return $this->tpl;
	}
	// <img src="images/member_czjl2_07.gif" />&nbsp;&nbsp;<a href="#">1</a> <a href="#">2</a> <a href="#">3</a> <a href="#">4</a> <a href="#">5</a> <a href="#">6</a>&nbsp;&nbsp;<img src="images/member_czjl2_09.gif" />
	function Style_6(){
		$t="{N2} {B} <a href=\"{U}\">[{N}]</a> ||| [{N}]{/B} {N3}";
		//重设语言包
		$this->L=array(
		"N2"=>'<img src="images/member_czjl2_07.gif"/>',
		"N3"=>'<img src="images/member_czjl2_09.gif"/>'
		);
		$this->getTemplate($t);
		$this->parseTemplate();
		$this->parseNumList();
		return $this->tpl;
	}
	/**********设置默认风格结束**********/
	/**********用户自定义风格开始**********/
	/*
	*方法：string getStyle($t)
	*作用：返回风格分页
	*说明：对外开放的接口，给用户自定义风格
	*例子：$s=getStyle("{N1} {N2} {N3} {N4}")
	*/
	function getStyle($t){
		$this->getTemplate($t);
		$this->parseTemplate();
		if($this->getTpl("L") || $this->getTpl("B")){$this->parseNumList();}
		return $this->tpl;
	}
	/**********用户自定义风格结束**********/
}