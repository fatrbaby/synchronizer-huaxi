<?php

namespace Bridge\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Slim\Container;
use Bridge\Model\Equipment;
use Bridge\Service\Differ\ArrayDiffer;

class SyncCommand extends Command
{
    const LOCATE_FILE = 'locate';
    const SYNC_API = 'http://erpif.yixiubao.cn/huaxi/equipment';
    
    protected $container = null;
    protected $equipment = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }
    
    public function configure()
    {
        $this->setName('sync');
        $this->setDescription('sync data');
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filenameLast = $this->getLastFile();
        $storage = $this->container->get('storage');
       
        if (!$filenameLast) {
            $this->updateLocate($this->makeDatabseFormattedFile());
            sleep(10); //to prevent double kill
            $filenameLast = $this->getLastFile();
        }
        
        $filename = $this->makeDatabseFormattedFile();
        $this->updateLocate($filename);
        $currentHash = md5_file($storage->absolute($filename));
        $lastHash = md5_file($storage->absolute($filenameLast));
       
        if (strcmp($currentHash, $lastHash) === 0) {
            $output->writeln('no changed');
            return;
        }
        
        $bench = new \Ubench;
        $bench->start();
        //to sync
        $differ = new ArrayDiffer();
        
        $current = $storage->read($filename);
        $last = $storage->read($filenameLast);
        
        if (!$differ->isDiffered()) {
            $differ->diff($current, $last);
        }
        
        $httpClient = $this->container->get('http');
        $equipment = $this->getEquipment();
        
        $removed = $differ->getRemoved();
        
        foreach ($removed as $id) {
            $response = $httpClient->request("DELETE", self::SYNC_API, ['query'=>['asset'=>$id]]);
            $output->writeln(sprintf('REMOVE:%s(%s)', $id, $response->getStatusCode()));
        }
        
        $modified = $differ->getModified();
        
        foreach ($modified as $id) {
           $query = $equipment->findByAssetNumber($id);
            $response = $httpClient->request("PUT", self::SYNC_API, ['query'=>$query]);
            $output->writeln(sprintf('MODIFY:%s(%s)', $id, $response->getStatusCode()));
        }
        
        $added = $differ->getAdded();
        
        foreach ($added as $id) {
            $query = $equipment->findByAssetNumber($id);
            $response = $httpClient->request("POST", self::SYNC_API, ['query'=>$query]);
            $output->writeln(sprintf('ADD:%s(%s)', $id, $response->getStatusCode()));
        }
        
        $bench->end();
        
        $output->writeln($bench->getTime() . '/' . $bench->getMemoryPeak());
    }
    
    protected function getLastFile()
    {       
        return $this->container->get('storage')->read(self::LOCATE_FILE);
    }
    
    protected function updateLocate($data)
    {
        return $this->container->get('storage')->write(self::LOCATE_FILE, $data);
    }
    
    protected function makeDatabseFormattedFile()
    {
        $equipment = $this->getEquipment();
        
        $filename = $this->generateFilename();
        $this->container->get('storage')->write($filename, $equipment->getHashedEquipments());
        
        return $filename;
    }
    
    protected function generateFilename()
    {
        return date('Ymdhis') . mt_rand(10000, 99999);
    }
    
    protected function getEquipment()
    {
        if ($this->equipment === null) {
            $this->equipment = new Equipment($this->container);
        }
        
        return $this->equipment;
    }
}
