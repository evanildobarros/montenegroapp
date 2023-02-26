<?php

namespace Winsite\File\Writer;

use Cake\Utility\Hash;
use Josegonzalez\Upload\File\Writer\DefaultWriter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use UnexpectedValueException;

/**
 * Writer para criar pastas com permissões publicas
 */
class WinsiteWriter extends DefaultWriter
{
    /**
     * Retrieves a configured filesystem for the given field
     *
     * @param string $field the field for which data will be saved
     * @param array $settings the settings for the current field
     * @return \League\Flysystem\FilesystemOperator
     */
    public function getFilesystem(string $field, array $settings = []): FilesystemOperator
    {
        $visibility = new PortableVisibilityConverter(
            0644,
            0600,
            0755,
            0700,
            Visibility::PUBLIC
        );
        $adapter = new LocalFilesystemAdapter(
            Hash::get($settings, 'filesystem.root', ROOT . DS),
            $visibility
        );
        $adapter = Hash::get($settings, 'filesystem.adapter', $adapter);
        if (is_callable($adapter)) {
            $adapter = $adapter();
        }

        if ($adapter instanceof FilesystemAdapter) {
            return new Filesystem($adapter, Hash::get($settings, 'filesystem.options', [
                'visibility' => Visibility::PUBLIC,
            ]));
        }

        throw new UnexpectedValueException(sprintf('Invalid Adapter for field %s', $field));
    }
}
