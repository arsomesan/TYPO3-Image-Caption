# Automatic TYPO3 Image Caption
Automatically generate image captions for alternative text using 
<a href="https://github.com/IBM/MAX-Image-Caption-Generator">MAX-Image-Caption-Generator</a>.

## Installation
Since the extension is just an proof of concept its not in any Composer Repository.
<br>
So clone the Extension into the packages directory in your TYPO3 Project and install with:

    composer require asom/image-caption:@dev

Change API url in /Classes/Command/CaptionAllImagesCommand.php line 59:

    CURLOPT_URL => 'http://yourapiurl:5000/model/predict'

## What it does
This extension adds the command:
    
    typo3cms imagecaption:captionall

This command will automatically fill the alternative text in the TYPO3 database with values generated from <a href="https://github.com/IBM/MAX-Image-Caption-Generator">MAX-Image-Caption-Generator</a> using the Images from the File-Browser.

## Credits
- [MAX-Image-Caption-Generator](https://github.com/IBM/MAX-Image-Caption-Generator)
