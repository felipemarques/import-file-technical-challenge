<?php

namespace App\Service;

use App\Repository\ObjectsRepository;
use App\Repository\SectorsRepository;
use App\Entity\Fields;
use App\Entity\ObjectProps;
use App\Entity\Objects;
use App\Entity\Sectors;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class ImportService
{
    protected ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) 
    {
        $this->doctrine = $doctrine;
    }

    public function addOrGetSector($name)
    {
        $sectorRepository = $this->doctrine->getRepository(Sectors::class);
        $sector = $sectorRepository->findOneBy(['name' => $name]);

        if (!$sector) {

            $sector = new Sectors();
            $sector->setName($name);

            $sectorRepository->add($sector, true);
        }

        return $sector;
    }

    public function addOrGetField($name)
    {
        $fieldRepository = $this->doctrine->getRepository(Fields::class);
        $field = $fieldRepository->findOneBy(['name' => $name]);

        if (!$field) {

            $field = new Fields();
            $field->setName($name);
            $field->setDataType('none');
            $field->setOptions('none');
            $field->setCreatedAt(new \DateTimeImmutable('now'));
            $field->setUpdatedAt(new \DateTimeImmutable('now'));

            $fieldRepository->add($field, true);
        }

        return $field;
    }

    public function process()
    {
        $entityManager = $this->doctrine->getManager();

        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($_FILES['file']['tmp_name']);

        foreach ($reader->getSheetIterator() as $sheet) {

            foreach ($sheet->getRowIterator() as $i => $row) {
                if ($i === 1) continue; // ignore the first now

                $cells = $row->getCells();

                $oid = $cells[0]->getValue();
                $sectorName = $cells[1]->getValue();
                $latitude = $cells[2]->getValue();
                $longitutde = $cells[3]->getValue();
                $manufacturer = $cells[4]->getValue();
                $model = $cells[5]->getValue();
                $voltage = $cells[6]->getValue();
                //$code = Uuid::v4(); // needs to be a size equals 50
                $categoryId = 0;

                $sector = $this->addOrGetSector($sectorName);

                $fieldManufacturer = $this->addOrGetField('Manufacturer');
                $fieldModel = $this->addOrGetField('Model');
                $fieldVoltage = $this->addOrGetField('Voltage');

                $object = new Objects();
                $object->setCategoryId($categoryId); // needs to map
                $object->setSectorId($sector->getId());
                $object->setOid($oid);
                //$object->setCode($code); // i forgot to add this into entity and migration
                $object->setLatitude($latitude);
                $object->setLongitude($longitutde);
                $object->setCreatedAt(new \DateTimeImmutable('now'));
                $object->setUpdatedAt(new \DateTimeImmutable('now'));



                $entityManager->persist($object);
                $entityManager->flush();

                print_r($object);
                exit;

                $objectProps = new ObjectProps();
                $objectProps->setObjectId($object->getId());
                $objectProps->setFieldId($fieldManufacturer->getId());
                $objectProps->setValue($manufacturer);

                $objectProps = new ObjectProps();
                $objectProps->setObjectId($object->getId());
                $objectProps->setFieldId($fieldModel->getId());
                $objectProps->setValue($model);

                $entityManager->persist($objectProps);
                $entityManager->flush();
        
            }
        }

        $reader->close();
    }
}
