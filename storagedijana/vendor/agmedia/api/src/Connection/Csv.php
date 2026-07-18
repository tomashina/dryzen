<?php

namespace Agmedia\Api\Connection;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 *
 */
class Csv
{
    
    /**
     * @var string|null
     */
    protected $path = null;

    /**
     * @var Worksheet
     */
    public $csv;
    
    
    /**
     * @param string|null $path
     *
     * @throws Exception
     */
    public function __construct(string $path = null)
    {
        $this->path = $path;
        $this->setFile();
    }
    
    
    /**
     * @return Worksheet
     */
    public function getCsv(): Worksheet
    {
        return $this->csv;
    }
    
    
    /**
     * @return Collection
     */
    public function collect(): Collection
    {
        return collect($this->csv->toArray());
    }
    
    
    /**
     * @param string $type
     *
     * @return $this
     * @throws Exception
     */
    public function setFile(string $type = 'Csv'): Csv
    {
        if ($this->path) {
            $reader    = IOFactory::createReader($type);
            $this->csv = $reader->load($this->path)->getActiveSheet();
        }
        
        return $this;
    }
}