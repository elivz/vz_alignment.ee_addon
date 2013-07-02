VZ Alignment
=====================

Fieldtype for setting horizontal, vertical, or two-dimensional alignment. Also works as a Matrix celltype.

Template Tags
-------------

### Single Tag ###

    {field_name [separator="-"] [multiple_separator="SPACE"]}

* `separator` - In a two-dimensional alignment field, this controls what appears between the two dimensions. Because EE strips spaces out of tag parameters, you must use `SPACE` instead. For example `separator="SPACE"` would output something like `left center`. (Default: "-")
* `multiple_separator` - If you allow more than one alignment to be selected, this controls what appears between each alignment. Because EE strips spaces out of tag parameters, you must use `SPACE` instead. (Default: "SPACE")

### Tag Pair ###

    {field_name}
        {horizontal} / {vertical}<br>
    {/field_name}

There are just two tags available within the tag pair: `{horizontal}` - which will be one of "left", "center", or "right" - and `{vertical}` - which will be "top", "center", or "bottom". In a single-dimensional field, the other dimension will always be "center".