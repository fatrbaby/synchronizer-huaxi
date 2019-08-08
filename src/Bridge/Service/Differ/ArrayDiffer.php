<?php

namespace Bridge\Service\Differ;

/**
 * Description of ArrayDiffer
 *
 * @author fatrbaby
 */
class ArrayDiffer 
{
    protected $added = [];
    protected $removed = [];
    protected $modified = [];
    
    private $differed = false;

    public function diff(array $current, array $last)
    {  
        $removed = $added = $modified = [];
        
        foreach ($current as $asset => $hash) {
            if (isset($last[$asset])) {
                if ($last[$asset] != $hash) {
                    $modified[] = $asset;
                }
            } else {
                $added[] = $asset;
            }
        }
        
        foreach ($last as $asset => $hash) {
            if (!isset($current[$asset])) {
                $removed[] = $asset;
            }
        }
        
        $this->removed = $removed;
        $this->modified = $modified;
        $this->added = $added;
        $this->differed = true;
    }
    
    public function getAdded()
    {
        return $this->added;
    }
    
    public function getModified()
    {
        return $this->modified;
    }
    
    public function getRemoved()
    {
        return $this->removed;
    }
    
    public function isDiffered()
    {
        return $this->differed;
    }
}
