# S3 module for Magento

A module that when enabled is supposed to replace all media related operations from working on disk, to work on S3.

This obviously comes with some caveats, such as resizing not being done on demand as part of templates anymore. Instead, resize configurations are set up in admin and then referenced in templates. The actual resizing is done on upload or by a background job.

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

In this specific case the bucket URL to use in media could look something like:

```
https://s3-eu-west-1.amazonaws.com/magento-media/media/
```

## Notes

There is one core copy-paste of Varien_Io_File. The reason is that the chdir() calls needed to have supressed warnings in order for the S3 magic to work. As far as I can see there is no issue with supressing these warnings. 
