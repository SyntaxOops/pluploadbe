$(function () {
  var filters = {
    max_file_size: Plupload_BE.settings.maxFileSize
  };

  if (Plupload_BE.settings.allowedExtensions.length > 0) {
    filters['mime_types'] = [{
      title: "Allowed files",
      extensions: Plupload_BE.settings.allowedExtensions
    }];
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
