<?php

namespace App\Controller;

use App\Entity\File as FileEntity;
use App\Entity\FileSize as FileSizeEntity;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FileController extends BaseController
{

    public function showAction()
    {
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        /** @var \Zend\Http\PhpEnvironment\Response $response */
        $response = $this->getResponse();

        $lastModified = $request->getHeader('If-Modified-Since');
        if ($lastModified) {
            $response->setStatusCode(304);

            return $response;
        }

        $default = trim($this->params()->fromQuery('default', ''));
        $size    = trim($this->params()->fromQuery('size', null));
        $id      = intval($this->params()->fromRoute('id'));
        if (!$id && !$default) {
            $response->setStatusCode(404);

            return $response;
        }

        $path = '';

        $fileRep = $this->getEntityManager()->getRepository(FileEntity::class);
        /** @var FileEntity $file */
        if ($id && ($file = $fileRep->findOneBy(['id' => $id]))) {
            $fileSizes = $file->getSizes();
            if ($size && count($fileSizes) > 0) {
                /** @var FileSizeEntity $fileSize */
                foreach ($fileSizes as $fileSize) {
                    if ($fileSize->getAlias() == $size) {
                        $path = $file->getFolder() . DIRECTORY_SEPARATOR . $fileSize->getName();
                    }
                }
            } else {
                $path = $file->getFolder() . DIRECTORY_SEPARATOR . $file->getName();
            }
        } elseif ($default) {
            $path = $default;
        } else {
            $response->setStatusCode(404);

            return $response;
        }

        $this->outputFile($path);
        die;
    }

    protected function outputFile($path)
    {
        $maxSpeed = 1024 * 8;

        /** @var \BsbFlysystem\Service\FilesystemManager $fsm */
        $fsm = $this->getEvent()->getApplication()->getServiceManager()->get(\BsbFlysystem\Service\FilesystemManager::class);
        /** @var \League\Flysystem\Filesystem $fs */
        $fs = $fsm->get('uploads');

        header("Cache-Control: public");
        header("Content-Transfer-Encoding: binary");
        header("Accept-Ranges: bytes");
        header("Cache-Control: max-age=, must-revalidate");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 7776000) . " GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $fs->getTimestamp($path)) . " GMT");
        header('Content-Type: ' . $fs->getMimetype($path));

        $range = 0;
        $size  = $fs->getSize($path);
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            str_replace($range, "-", $range);
            $size2      = $size - 1;
            $new_length = $size - $range;
            header("HTTP/1.1 206 Partial Content");
            header("Content-Length: {$new_length}");
            header("Content-Range: bytes $range$size2/$size");
        } else {
            $size2 = $size - 1;
            header("Content-Range: bytes 0-$size2/$size");
            header("Content-Length: " . $size);
        }

        if ($size == 0) {
            die('Zero byte file! Aborting download');
        }

        $stream = $fs->readStream($path);

        fseek($stream, $range);

        while (!feof($stream) and (connection_status() == 0)) {
            set_time_limit(0);
            print(fread($stream, 1024 * $maxSpeed));
            flush();
            ob_flush();
            sleep(1);
            //usleep(250000);
        }
        fclose($stream);
        die;
    }
}
