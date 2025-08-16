$(function () {

  // https://github.com/moxiecode/plupload/issues/1242
  plupload.addFileFilter('exclude_mime_types', function(filter, file, cb){
    var permitted = true;
    var extensions = filter[0].extensions.split(',');
    // We have no excluded extensions, function presented default exclusion string, so allow anything
    if(extensions.length === 1 && extensions[0] === "-")
      permitted = true;
    else
    {
      for(var i = 0; i < extensions.length; i++)
      {
        var fileArray = file.name.split('.');
        var extension = fileArray[fileArray.length - 1];

        if(extensions[i].trim().toUpperCase() === extension.toUpperCase())
        {
          this.trigger('Error', {
            code: plupload.FILE_EXTENSION_ERROR,
            message: plupload.translate('File extension error.'),
            file: file
          });
          permitted = false;
          cb(false);
          return;
        }
      }
    }

    if(permitted)
      cb(true);
  });

  var filters = {
    max_file_size: Plupload_BE.settings.maxFileSize,
    prevent_duplicates: true
  };

  // List of allowed files extensions
  if (Plupload_BE.settings.allowedExtensions.length > 0) {
    filters['mime_types'] = [
      {
        title: "Allowed files",
        extensions: Plupload_BE.settings.allowedExtensions,
      }
    ];
  }

  // List of excluded files extensions
  if (Plupload_BE.settings.excludedExtensions.length > 0) {
    filters['exclude_mime_types'] = [
      {
        title: "Excluded files",
        extensions: Plupload_BE.settings.excludedExtensions,
      }
    ];
  }

  var settings = {
    runtimes: 'html5,flash,silverlight,html4',
    url: Plupload_BE.settings.uploadUrl,
    chunk_size: Plupload_BE.settings.chunkSize,
    unique_names: false,
    rename: true,
    dragdrop: true,
    multiple_queues: true,
    prevent_duplicates: true,
    filters: filters,
    flash_swf_url: Plupload_BE.settings.extDir + 'Resources/Public/JavaScript/plupload/js/Moxie.swf',
    silverlight_xap_url: Plupload_BE.settings.extDir = 'Resources/Public/JavaScript/plupload/js/Moxie.xap',
  };

  if (Plupload_BE.settings.resizeEnabled) {
    settings['resize'] = Plupload_BE.settings.resize;
  }

  // Post init events, bound after the internal events
  settings['init'] = {
    Error: function (up, error) {
      json = $.parseJSON(error.response);
      if (json.error) {
        error.message += " " + json.error.message;
      }

      $.notify(error.message, "error");
    },
    FileUploaded: function (up, file, info) {
      json = $.parseJSON(info.response);
      if (json.error) {
        file.status = plupload.FAILED;
        errorMsg += json.error.message;
        $.notify(errorMsg, "error");
      } else {
        $.notify('File uploaded successfully !', "success");
      }
    }
  };

  var json, uploader = $('#uploader_' + Plupload_BE.settings.uid).pluploadQueue(settings);
});
