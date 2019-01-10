<?php
namespace AppBundle\Service;

use VideoPlayerBundle\Entity\YouTubeAnalytics;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public function getResponseFromQueryBuilder(QueryBuilder $queryBuilder, $columns, $filename)
    {
        $entities = new ArrayCollection($queryBuilder->getQuery()->getResult());
        $response = new StreamedResponse();
        if (is_string($columns)) {
            $columns = $this->getColumnsForEntity($columns);
        }
        $response->setCallback(function () use ($entities, $columns) {
            $handle = fopen('php://output', 'w+');
            // Add header
            fputcsv($handle, array_keys($columns));
            while ($entity = $entities->current()) {
                $values = [];
                foreach ($columns as $column => $callback) {
                    $value = $callback;
                    if (is_callable($callback)) {
                        $value = $callback($entity);
                    }
                    $values[] = $value;
                }
                fputcsv($handle, $values);
                $entities->next();
            }
            fclose($handle);
        });
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }
    private function getColumnsForEntity($class)
    {
    }
}
