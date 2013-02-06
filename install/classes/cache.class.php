<?php    
/**********************************************************    
用法:    
$cache=new    Cache();    
$cache->startToCacheFile()        //自动处理缓存是否开启以及缓存是否存在,进行相应的处理    
.......    
页面内容    
.......    
$cache->saveToCacheFile();    
**********************************************************/    
        
/*********************************************************************    
*$this->CachePath                    模板缓存路径    
*$this->CacheEnable                    自动缓存机制是否开启，未定义或为空，表示关闭自动缓存机制    
*ReCacheTime                    自动重新缓存间隔时间，单位为秒，未定义或为空，表示关闭自动重新缓存    
*********************************************************************/    
        
class    Cache    
{    
private    $CachePath='cache';
private    $CacheEnable=false;
private    $ReCacheTime=3600;
private    $cachefile;    
private    $cachefilevar;    
private    $startTime;  
        
/*********************************************************************    
*生成当前页的Cache组文件名    $this->cachefilevar    及文件名    $this->cachefile    
*动态页的参数不同对应的Cache文件也不同，但是每一个动态页的所有Cache文件都有相同的文件名，只是扩展名不同    
*********************************************************************/    
public function    Cache($_CachePath='cache',$_CacheEnable=false,$_ReCacheTime=3600,$_pageUrl='')        
{    
        $this->CacheEnable=$_CacheEnable;
        $this->ReCacheTime=$_ReCacheTime;
		$this->CachePath  =$_CachePath;
        $this->init($_pageUrl);
}
public function init($_pageUrl=''){
	    if($_pageUrl==''){
			$_pageUrl=$_SERVER["REQUEST_URI"];
			}
	   if(strpos($_pageUrl,'?')===false){
			$_filepath=$_pageUrl;
			}else{
				$pp=explode('?',$_pageUrl);
				$_filepath=$pp[0];
				}
		$s=array(".","/");
		$r=array("_","");
		$t=array($_filepath,'?','&','/');
        $this->cachefilevar=str_replace($s,$r,$_filepath)."_".str_replace($t,'',$_pageUrl);    
        $this->cachefile=$this->cachefilevar.".".md5($_pageUrl);
		
		$path=substr(md5($_pageUrl),0,3);
		$this->CachePath=$this->CachePath.'/'.$path[0].'/'.$path[1].'/'.$path[2];
		if(!is_dir($this->CachePath)){
			$this->dmkdir($this->CachePath);
			}
	}
//删除当前页/模块的缓存    
public function    cleanCacheFile()        
{    
        //删除当前页的缓存    
        $d    =    dir($this->CachePath);    
        $strlen=strlen($this->cachefilevar);    
        //返回当前页的所有Cache文件组    
        while    (false    !==    ($entry    =    $d->read()))        
        {    
            if    (substr($entry,0,$strlen)==$this->cachefilevar)        
            {    
                    if    (!unlink($this->CachePath."/".$entry))        
            {    
                    echo    "Cache目录无法写入";    
                    exit;    
                    }    
                    }    
            }    
        }    
        
//判断是否已Cache过，以及是否需要Cache    
public function    checkCacheFile()    
{    
        //如果设置了缓存更新间隔时间    $this->ReCacheTime    
        if    ($this->ReCacheTime+0>0)                    
        {    
            //返回当前页Cache的最后更新时间    
            $var=@file($this->CachePath."/".$this->cachefilevar);
			$var=$var[0];    
                    //如果更新时间超出更新间隔时间则删除Cache文件    
                    if    (time()-$var>$this->ReCacheTime)        
            {    
                        $this->cleanCacheFile();    
            $ischage=true;    
                    }    
            }    
            //返回当前页的Cache    
            $file=$this->CachePath."/".$this->cachefile;    
        //判断当前页Cache是否存在    且    Cache功能是否开启    
        return    (file_exists($file)    and    $this->CacheEnable    and    !$ischange);    
}    
        
//读取Cache    
private function    echoCache()    
{    
        //返回当前页的Cache    
        $file=$this->CachePath."/".$this->cachefile;    
        //读取Cache文件的内容    
        if    ($this->CacheEnable)        
        return    readfile($file);    
            else        
                    return    false;    
        }    
        
//开始缓存    
public function    startToCacheFile()    
        {    
        if    ($this->checkCacheFile())    
        {    
            //$this->startTimer();    //开始记录直接输出缓存内容所用的时间    
            $this->echoCache();    
        //echo    $this->spendTime();//结果记录直接输出缓存内容所用的时间    
        exit;    
            }    
                    else        
                    {        
                    //$this->startTimer();    //开始记录第一次缓存内容所用的时间    
                        ob_clean();    
                        ob_start();    
                        ob_implicit_flush(0);    
            }    
        }    
        
        
//生成Cache    
public function    saveToCacheFile()    
{        
        //取得当前页面的内容    
        $output    =    ob_get_contents(); 
        //返回当前页的Cache    
        $file=$this->CachePath."/".$this->cachefile;    
        //如果Cache功能开启    
        if    ($this->CacheEnable)        
        {    
                    //把输出的内容写入Cache文件    
                    $fp=@fopen($file,'w');    
                    if    (!@fputs($fp,$output))        
            {    
                    echo    "模板Cache写入失败";    
            exit;    
            }    
                    @fclose($fp);    
                    //如果设置了缓存更新间隔时间    $this->ReCacheTime    
                    if    ($this->ReCacheTime+0>0)        
                    {    
                        //更新当前页Cache的最后更新时间    
                        $file=$this->CachePath."/".$this->cachefilevar;    
                        $fp=@fopen($file,'w');    
                        if    (!@fwrite($fp,time()))        
                    {    
                        echo    "Cache目录无法写入";    
                        exit;    
                    }    
                        @fclose($fp);    
                    }    
        //$this->spendTime();//结果记录第一次缓存内容所用的时间        
            }    
        }
public function dmkdir($dir){         //创建目录
$dir=explode('/',$dir);
$dcount=count($dir);
$cachethreaddir2='';
for($k=0;$k<$dcount;$k++){
	$cachethreaddir2.=$dir[$k].'/';
if(!is_dir($cachethreaddir2)) {
					@mkdir($cachethreaddir2, 0777);
				}
}
	}
        
}    //end    the    cache    Class    