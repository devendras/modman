# Helpers related to assets go here...

# <%= css_asset 'css'  %>
# <%= js_asset 'js'  %>
# <%= img_asset 'img'  %>
# <%= vendor_asset 'foo/bar.js'  %>

def css_asset(path)
  asset_path :css, path
end

def js_asset(path)
  asset_path :js, path
end

def img_asset(path)
  asset_path :images, path
end

def vendor_asset(path)
  asset_folder = settings.vendor_dir
  asset_url(path, asset_folder)
end

def javascript_tag(path)
  content_tag(:script, nil, {
    :src=>path,
    :type=>"text/javascript"
  })
end
