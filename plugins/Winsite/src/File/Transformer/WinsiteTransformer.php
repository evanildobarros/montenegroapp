<?php
declare(strict_types=1);

namespace Winsite\File\Transformer;

use Cake\ORM\Locator\TableLocator;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use Josegonzalez\Upload\File\Transformer\DefaultTransformer;

/**
 * Class WinsiteTransformer
 *
 * @package App\File\Transformer
 */
class WinsiteTransformer extends DefaultTransformer
{
    /**
     * Cuts the image keeping the aspect ratio
     */
    public const MODE_KEEP_RATIO = 'keep_ratio';

    /**
     * Cuts the image cropping the extra pixels
     */
    public const MODE_CROP = 'crop';

    /**
     * Resize the image. May cause distortion
     */
    public const MODE_NORMAL = 'normal';

    /**
     * Places the watermark in the center of the image
     */
    public const WATERMARK_POSITION_CENTER = 'center';

    /**
     * Places the watermark in the top of the image
     */
    public const WATERMARK_POSITION_TOP = 'top';

    /**
     * Places the watermark in the left corner of the image
     */
    public const WATERMARK_POSITION_LEFT = 'left';

    /**
     * Places the watermark in the bottom of the image
     */
    public const WATERMARK_POSITION_BOTTOM = 'bottom';

    /**
     * Places the watermark in the right corner of the image
     */
    public const WATERMARK_POSITION_RIGHT = 'right';

    /**
     * Places the watermark in the top left position
     */
    public const WATERMARK_POSITION_TOP_LEFT = 'top-left';

    /**
     * Places the watermark in the top right position
     */
    public const WATERMARK_POSITION_TOP_RIGHT = 'top-right';

    /**
     * Places the watermark in the bottom left position
     */
    public const WATERMARK_POSITION_BOTTOM_LEFT = 'bottom-left';

    /**
     * Places the watermark in the bottom right position
     */
    public const WATERMARK_POSITION_BOTTOM_RIGHT = 'bottom-right';

    /**
     * Defines the watermark size in absolute pixels
     */
    public const WATERMARK_SIZE_TYPE_PIXELS = 'px';

    /**
     * Defines the watermark size in a percentage relative to the size of the processed image
     */
    public const WATERMARK_SIZE_TYPE_PERCENTAGE = '%';

    /**
     * Creates a set of files from the initial data and returns them as key/value
     * pairs, where the path on disk maps to name which each file should have.
     * Example:
     *
     *   [
     *     '/tmp/path/to/file/on/disk' => 'file.pdf',
     *     '/tmp/path/to/file/on/disk-2' => 'file-preview.png',
     *   ]
     *
     * @param string $filename Filename.
     * @return array key/value pairs of temp files mapping to their names
     * @throws \Exception
     */
    public function transform(string $filename): array
    {
        // Obtém a extensão original da imagem
        $extension = pathinfo($this->data->getClientFilename(), PATHINFO_EXTENSION);

        $tableLocator = new TableLocator();
        /** @var \App\Model\Table\ConfigsTable $configTable */
        $configTable = $tableLocator->get('Configs');
        $extraSizes = [];

        // Inicializa cada um dos tamanhos
        if (!empty($this->settings['sizes'])) {
            foreach ($this->settings['sizes'] as $name => $setting) {
                $tmp = tempnam(sys_get_temp_dir(), $name) . '.' . $extension;

                $manager = new ImageManager([
                    'driver' => env('IMAGE_ENGINE', 'gd'),
                ]);

                $image = $manager->make($this->data->getStream());

                switch ($setting['mode']) {
                    case self::MODE_KEEP_RATIO: // Corta a imagem mantendo a proporção de altura e largura
                        $image = $image
                            ->resize($setting['w'], $setting['h'], function (Constraint $constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            })
                            ->encode($extension);
                        break;
                    case self::MODE_CROP: // Corta a imagem ignorando a altura e largura
                        $image = $image
                            ->crop($setting['w'], $setting['h'])
                            ->encode($extension);
                        break;
                    case self::MODE_NORMAL: // Redimensiona a imagem, podendo haver distorções
                        $image = $image
                            ->resize($setting['w'], $setting['h'])
                            ->encode($extension);
                        break;
                    default:
                        throw new \InvalidArgumentException('Invalid mode!');
                }

                if (!empty($setting['watermarks'])) {
                    foreach ($setting['watermarks'] as $watermarkSetting) {
                        $managerWatermark = $manager;
                        if ($watermarkSetting['sizeType'] == self::WATERMARK_SIZE_TYPE_PERCENTAGE) {
                            $w = $setting['w'] * $watermarkSetting['w'] / 100;
                            $h = $setting['h'] * $watermarkSetting['h'] / 100;
                        } elseif ($watermarkSetting['sizeType'] == self::WATERMARK_SIZE_TYPE_PIXELS) {
                            $w = $watermarkSetting['w'];
                            $h = $watermarkSetting['h'];
                        } else {
                            throw new \Exception('Invalid size type!');
                        }
                        $watermark = $managerWatermark
                            ->make($watermarkSetting['image'])
                            ->resize($w, $h, function (Constraint $constraint) {
                                $constraint->aspectRatio();
                            });
                        $image = $image->insert(
                            $watermark->encode(),
                            $watermarkSetting['position'],
                            $watermarkSetting['x'],
                            $watermarkSetting['y']
                        );
                    }
                }

                $image->save($tmp, $configTable->parametro('qualidade_imagem', 90));

                $extraSizes[$tmp] = "{$name}-{$this->data->getClientFilename()}";
            }
        }
        $result = [
            $this->data->getStream()->getMetadata('uri') => $filename,
        ];

        return $result + $extraSizes;
    }
}
