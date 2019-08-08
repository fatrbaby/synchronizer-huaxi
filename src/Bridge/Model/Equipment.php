<?php

namespace Bridge\Model;

use Bridge\Response\ErrorException;

class Equipment extends Model
{

    public function findByAssetNumber($number)
    {
        if (!$this->validateAssetNumber($number)) {
            throw new ErrorException(38);
        }
        
        $sql = sprintf("SELECT * FROM huaxi.dbo.equipment WHERE equi_arch_no='%s'", $number);
        $raw = "SELECT * FROM huaxi.dbo.equipment WHERE equi_arch_no=?";
        
        $stmt = $this->getDb()->prepare($sql);
        $stmt->bindValue(1, $number);
        $stmt->execute();

        $record = $stmt->fetch(\PDO::FETCH_ASSOC);
		
        if ($record === false) {
            throw new ErrorException(39);
        }

        $equipment = [
            'asset_number' => isset($record['equi_arch_no']) ? $record['equi_arch_no'] : '',
            'name' => isset($record['equi_name']) ? $record['equi_name'] : '',
            'category' => isset($record['equi_spec']) ? $record['equi_spec'] : '',
            'brand' => isset($record['equi_brand']) ? $record['equi_brand'] : '',
            'type' => isset($record['equi_model']) ? $record['equi_model'] : '',
            'department' => isset($record['card_palce']) ? $record['card_palce'] : '',
            'factory' => isset($record['factory_name']) ? $record['factory_name'] : '',
        ];
		
        $converted = array_map(function ($value) {
            return mb_convert_encoding($value, 'UTF-8', 'GBK');
        }, $equipment);

        return $converted;
    }
    
    public function getHashedEquipments()
    {
        $smtm = $this->getDb()->prepare("SELECT [equi_arch_no], [equi_name]+[equi_model]+[equi_spec]+[equi_brand]+[card_palce]+[factory_name] AS contact FROM [huaxi].[dbo].[equipment]");
        $smtm->execute();
        $equipments = $smtm->fetchAll(\PDO::FETCH_ASSOC);
        
        $hashed = [];
        
        foreach ($equipments as $equipment) {
            $hashed{$equipment['equi_arch_no']} = md5($equipment['contact']);   
        }
        
        return $hashed;
    }


    public function validateAssetNumber($number)
    {
        if (strlen($number) != 15) {
            return false;
        }
        
        return strncasecmp($number, 'ZCKP', 4) == 0;
    }
}
