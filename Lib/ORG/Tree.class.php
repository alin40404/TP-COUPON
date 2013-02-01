<?php
/**
* author: askie
* blog: http://www.pkphp.com
* 版权： 随便用
* 无限分类
*/
class Tree
{
    public $data        =   array();
    public $cateArray   =   array();
    public $order       =   array();
    
    public function setNode ($id, $parent, $value,$order=1)
    {
        $parent = $parent?$parent:0;
        $this->data[$id] = $value;
        $this->cateArray[$id] = $parent;
        $this->order[$id]=$order;
    }
    
    public function getChildsTree($id=0)
    {
        $childs=array();
        foreach ($this->cateArray as $child=>$parent)
        {
            if ($parent==$id)
            {
                $childs[$child]=$this->getChildsTree($child);
            }

        }
        return $childs;
    }
    
    public function getChilds($id=0)
    {
        $childArray=array();
        $childs=$this->getChild($id);
        foreach ($childs as $key=>$child)
        {
            $childArray[]=$key;
            $childArray=array_merge($childArray,$this->getChilds($key));
        }
        return $childArray;
    }
    
    public function getChild($id)
    {
        $childs=array();
        foreach ($this->cateArray as $child=>$parent)
        {
            if ($parent==$id)
            {
                $childs[$child]=$this->order[$child];
            }
        }
        asort($childs);
        return $childs;
    }
    
    //单线获取父节点
    public function getNodeLever($id)
    {
        $parents=array();
        if (key_exists($this->cateArray[$id],$this->cateArray))
        {
            $parents[]=$this->cateArray[$id];
            $parents=array_merge($parents,$this->getNodeLever($this->cateArray[$id]));
        }
        return $parents;
    }
    
    public function getLayer($id,$preStr='|-')
    {
        return str_repeat($preStr,count($this->getNodeLever($id)));
    }
    
    public function getValue ($id)
    {
        return $this->data[$id];
    }
    
    public function getOrder($id){
        return $this->order[$id];
    }
    
    public function getFid($id){
        return $this->cateArray[$id];
    }
    
    public function getCateTree($id = 0)
    {
        static $res = array();
        if(isset($res[$id])){
            return $res[$id];
        }
        $cates = $this->getChilds($id);
        $res[$id] = array();
        $res[$id][] = array(
                    'id'    =>  $id,
                    'level' =>  count($this->getNodeLever($id)),
                    'name'  =>  $this->getValue($id),
                    'childs'    =>  array_values($this->getChilds($id)),
                    'parent_id' =>  $this->getFid($id),
                    'sort_order'	=>	$this->getOrder($id),
                    );
        foreach ($cates as $key=>$cateid)
        {
            $res[$id][] = array(
                    'id'    =>  $cateid,
                    'level' =>  count($this->getNodeLever($cateid)),
                    'name'  =>  $this->getValue($cateid),
                    'childs'    =>  array_values($this->getChilds($cateid)),
                    'parent_id' =>  $this->getFid($cateid),
                    'sort_order'	=>	$this->getOrder($cateid),
                    );
        }
        return $res[$id];
    }
}

/*
$Tree = new Tree();
//setNode(目录ID,上级ID，目录名字,排序);
$Tree->setNode(1, 0, '目录1',1);
$Tree->setNode(2, 1, '目录2',2);

//print_r($Tree->getChildsTree(0));
//print_r($Tree->getChild(0));
//print_r($Tree->getLayer(2));

$category = $Tree->getCateTree();
foreach ($category as $cate){
    echo str_repeat('|-',$cate['level']) . $cate['name'];
    echo "<br />";
}
*/