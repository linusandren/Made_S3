# S3/Kraken module for Magento

A module that when enabled is supposed to replace all media related operations from working on disk, to work on S3.

This obviously comes with some caveats, such as resizing not being done on demand as part of templates anymore. Instead, resize configurations are set up in admin and then referenced in templates. The actual resizing is done on upload or by a background job (TO DO).

## Magento Configuration

The S3 configuration is done in local.xml. Since we need to take over media management at an early stage, using the Media Storage option can't be used. The local.xml section is in global and should look something like:

```xml
<s3>
    <!--
        Should it be active? local.xml is read last so changing this
        has immediate effect.
    -->
    <active>1</active>
    
    <!-- The S3 access key ID -->
    <access_key_id>1234</access_key_id>
     
    <!-- The S3 access secret -->
    <access_secret>asdf</access_secret>
    
    <!-- The S3 bucket where we want to put all media -->
    <bucket_name>magento-media</bucket_name>
    
    <!-- The region where the bucket is, a full list of regions can
         be found here: http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region -->
    <region>us-east-1</region>

    <!-- Kraken.io API credentials -->
    <kraken_key>key</kraken_key>
    <kraken_secret>secret</kraken_secret>
</s3>
```

### URLs

Magento has a setting for Base Media URL which is normally used when working with CDN setups. In this case it is a little bit different since we have both a general media path and URL, and also a URL for all resized images.

In order to achieve this a new separate CDN URL setting has been added. The CDN URL needs to have gthe base URL of the CDN pointing to the S3 bucket origin. The CDN URL setting is in System / Config / Advanced / System / Storage Configuration for Media.
  
Apart from this, the S3 bucket origin needs to be set up as the general media URL. Unless this has been done, media inside of magento admin will not be displayed.

## S3 configuration

Apart from actually creating a bucket and generating access keys with write access to it, you also need to give it a public read policy. The reason for this is the above mentioned need to use the bucket URL as the normal media URL in magento. A public readable bucket policy can look like this:

```json
{
  "Version": "2008-10-17",
  "Statement": [{
    "Sid": "AllowPublicRead",
    "Effect": "Allow",
    "Principal": { "AWS": "*" },
    "Action": ["s3:GetObject"],
    "Resource": ["arn:aws:s3:::magento-media/*" ]
  }]
}
```

Note that the bucket name is in the "Resource" section.

In this specific case the bucket URL to use as media URL could look something like:

```
https://s3-eu-west-1.amazonaws.com/magento-media/media/
```

## Synchronizing an existing media library

Due to the fact that all media is stored with the exact same structure as they would on a file system, and that we also maintain URLs correctly, synchronizing media can simply be done with a recursive using something like s3cmd. Have a look at [s3cmd sync](http://s3tools.org/s3cmd-sync).

## Kraken Image Processing

Kraken is used for image processing and uploading to S3. This means all resize and processing settings are defined in advance, in `System / Configuration / Advanced / System / Amazon S3/Kraken/CDN Settings / Kraken Processing Settings`. Every type of image resize setting, whether it's processing of an arbitrary uploaded image or the batch resizing of product images, they need to be entered in there. When a product image is uploaded and the product is saved, all processing settings will be applied on the image, making sure everything is accessible in templates.

### Referencing Resized Product Images

In order to get an effective resize feature, all images are resized using Kraken when a product is saved with a new image in admin. This also meant that a new folder structure for resized images needed to be created. It also meant that a different method should be used in templates when displaysing images. The default image resizer touches the file system and creates one resized copy for every image. That's not very efficient and we don't need it at all since images are not resized on demand anymore.

To use the new resize URL helper, replace

```php
<?php
$this->helper('catalog/image')->init($_product, 'image');
```

with

```php
<?php
$this->helper('made_s3')->getImageCdnUrl($_product->getImage(), 'main_image');
```

In this example, a key "main_image" has been created as a Kraken processing setting in admin.

## Notes

There is one core copy paste of Varien_Io_File. The reason is that the chdir() calls needed to have supressed warnings in order for the S3 magic to work. As far as I can see there is no issue with supressing these warnings. 
