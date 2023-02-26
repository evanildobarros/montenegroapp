<?php
declare(strict_types=1);

namespace Winsite\Model\Entity;

use Cake\ORM\Entity;

/**
 * Class WinsiteUploadTrait
 *
 * @package Winsite\Model\Entity
 */
trait WinsiteUploadTrait
{
    /**
     * Retorna o caminho da imagem
     *
     * @param string $field Campo
     * @param string|null $size Tamanho
     * @return string
     */
    public function getImagem($field, $size = null)
    {
        if (!is_subclass_of($this, Entity::class)) {
            throw new \LogicException('This trait must only be used on an \Cake\ORM\Entity instance!');
        }

        if (!empty($size)) {
            $size = "{$size}-";
        }

        return "/files/{$this->getSource()}/{$field}/{$size}{$this->{$field}}";
    }
}
