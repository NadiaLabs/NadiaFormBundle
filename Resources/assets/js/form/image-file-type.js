import '../../scss/form/image-file-type.scss';

(function ($, undefined) {
    'use strict';

    $('.nadia-image-file-type').each(function () {
        let $node = $(this);
        let $uploadButton = $node.find('.upload-button');
        let $replaceButton = $node.find('.replace-button');
        let $deleteButton = $node.find('.delete-button');
        let $imageContainer = $node.find('.image-container');
        let $image = $imageContainer.find('img');

        $node.on('change', 'input[type="file"]', function() {
            $.each(this.files, function (i, file) {
                let reader = new FileReader();

                reader.onloadend = function() {
                    $image.attr('src', reader.result);
                    $image.parent().on('click', function (event) { event.preventDefault(); });
                    $imageContainer.show();
                    $uploadButton.hide();
                };

                reader.readAsDataURL(file);
            });
        });

        function getUploadInput() {
            return $node.find('input[type="file"]');
        }

        function changeImage(event) {
            event.preventDefault();

            getUploadInput().trigger('click');
        }

        $uploadButton.on('click', changeImage);
        $replaceButton.on('click', changeImage);
        $deleteButton.on('click', function (event) {
            event.preventDefault();

            getUploadInput().val('');
            getUploadInput().replaceWith(getUploadInput().clone(true));

            $imageContainer.hide();
            $uploadButton.show();

            $node.find('input[type="hidden"]').val('');
        });
    });
})(jQuery);
