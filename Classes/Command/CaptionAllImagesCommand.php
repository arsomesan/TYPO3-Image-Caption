<?php

declare(strict_types = 1);

namespace Asom\ImageCaption\Command;

use CURLFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

final class CaptionAllImagesCommand extends Command
{

    protected function configure(): void
    {
        $this->setDescription('Caption all Images automatically');
        $this->setHelp(
            <<<'EOF'
This Command will generate imagecaptions from the filebrowser image files and insert them into the alternative text in the database.
While doing that the command will overwrite all existing alternative texts.
EOF
        );
    }
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        assert($fileRepository instanceof FileRepository);
        $files = $fileRepository->findAll();
        $globalcounter = 0;
        foreach ($files as $file) {
            if($file->getExtension() == 'jpg' || $file->getExtension() == 'png' || $file->getExtension() == 'jpeg') {
                $globalcounter++;
            }
        }
        $output->writeln($globalcounter . ' Files found');
        $counter = 1;
        foreach ($files as $file) {
            if($file->getExtension() == 'jpg' || $file->getExtension() == 'png' || $file->getExtension() == 'jpeg') {
                $output->writeln('Generating Caption for image: ' . $counter . "/". $globalcounter ." - " . $file->getIdentifier() . ' - ' . $file->getName());
                //get filepath of image
                $path = "web/fileadmin";
                $path .= $file->getIdentifier();
                //get filename
                $filename = $file->getName();
                //get filetype
                $filetype = "image/";
                $filetype .= $file->getExtension();
                //get uid
                $uid = $file->getUid();
                //initalize curl request
                $curl = curl_init();
                //set curl options
                curl_setopt_array($curl, array(
                //change to ip of your instance of max image caption generator
                CURLOPT_URL => 'http://localhost:5000/model/predict',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('image'=> new CURLFile($path, $filetype, $filename)),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                if($response === false) {
                    $output->writeln("Result: FAILED" . "\n");
                    continue;
                }
                else {
                    //decode json to php object and take caption from first prediction
                    $responsedata = json_decode($response, true);
                    $caption = $responsedata["predictions"][0]["caption"];
                    $output->writeln("Result:" .  $caption. "\n");
                    //paste caption into alternative test in sys_file_metadata table
                    GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable("sys_file_metadata")
                        ->update(
                            'sys_file_metadata',
                            ['alternative' => $caption],
                            ['uid' => $uid]
                        );
                }
                $counter++;
            }
            else {
                continue;
            }
        }
        return self::SUCCESS;
    }
}