Add ”kraken_status” TEXT to catalog_product_entity_media_gallery to determine which versions of the image have been resized so we know they are available or not (in templates)
    Use a serialized array to contain all values
    Put a ”dispatch_date” field to stamp the date the resize event was dispatched to kraken
Create a helper method that takes a gallery, loops through it and crosschecks all kraken_status entries to see if any new resize needs to be dispatched. Empty status = dispatch all.
On media gallery save, pass the gallery to the helper method so it resizes
On media gallery load, pass the gallery to the helper method so it can be resized, use a guard to prevent this/make this happen when doing batch jobs
When dispatching the resize event, store a key with the resize array and kraken dispatch ID in mysql/kraken_status temporarily
When the callback_url is requested, update the kraken_status array to make sure that all images are available with regards to resize information
    - Fetch the resize information using the kraken dispatch ID
    - Update kraken_status
