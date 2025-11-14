<div class="variation-form-wrapper">
    <form action="">
        <div class="input-group py-3 ">
            <span class="input-group-text">Variation Title</span>
            <input type="text"
                value="{{ \Botble\Ecommerce\Models\ProductVariation::where('id', $productVariationsInfo?->first()?->variation_id)?->first()?->variation_title }}"
                class="form-control" name="variation_title" placeholder="Title">
        </div>
        @include('plugins/ecommerce::products.partials.product-attribute-sets')

        @include('plugins/ecommerce::products.partials.general', [
            'product' => $product,
            'originalProduct' => $originalProduct,
            'isVariation' => true,
        ])
        <div class="variation-images">
            @include('core/base::forms.partials.images', [
                'name' => 'images[]',
                'values' => isset($product) ? $product->images : [],
            ])
        </div>
        <div class="input-group mt-2">
            <span class="input-group-text">Variation Description</span>
            <textarea class="form-control variationDescription" name="variation_desc" aria-label="variation_desc">{{ \Botble\Ecommerce\Models\ProductVariation::where('id', $productVariationsInfo?->first()?->variation_id)?->first()?->variation_desc }}</textarea>
        </div>
    </form>

    <script>
        function customCkEditor(editor, extraConfig) {

            // const editor = document.querySelector('#' + element)

            let config = {
                fontSize: {
                    options: [9, 10, 11, 12, 13, 'default', 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
                },

                alignment: {
                    options: ['left', 'right', 'center', 'justify'],
                },

                heading: {
                    options: [{
                            model: 'paragraph',
                            title: 'Paragraph',
                            class: 'ck-heading_paragraph'
                        },
                        {
                            model: 'heading1',
                            view: 'h1',
                            title: 'Heading 1',
                            class: 'ck-heading_heading1'
                        },
                        {
                            model: 'heading2',
                            view: 'h2',
                            title: 'Heading 2',
                            class: 'ck-heading_heading2'
                        },
                        {
                            model: 'heading3',
                            view: 'h3',
                            title: 'Heading 3',
                            class: 'ck-heading_heading3'
                        },
                        {
                            model: 'heading4',
                            view: 'h4',
                            title: 'Heading 4',
                            class: 'ck-heading_heading4'
                        },
                        {
                            model: 'heading5',
                            view: 'h5',
                            title: 'Heading 5',
                            class: 'ck-heading_heading4'
                        },
                        {
                            model: 'heading6',
                            view: 'h6',
                            title: 'Heading 6',
                            class: 'ck-heading_heading4'
                        },
                    ],
                },
                placeholder: ' ',
                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'fontColor',
                        'fontSize',
                        'fontBackgroundColor',
                        'fontFamily',
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'strikethrough',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'alignment',
                        'direction',
                        'shortcode',
                        'outdent',
                        'indent',
                        '|',
                        'htmlEmbed',
                        'imageInsert',
                        'ckfinder',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'bootstrapGrid',
                        'undo',
                        'redo',
                        'findAndReplace',
                        'removeFormat',
                        'sourceEditing',
                        'codeBlock',
                        'fullScreen',
                    ],
                    shouldNotGroupWhenFull: true,
                },
                language: {
                    ui: window.siteEditorLocale || 'en',

                    content: window.siteEditorLocale || 'en',
                },
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:inline',
                        'imageStyle:block',
                        'imageStyle:side',
                        'imageStyle:wrapText',
                        'imageStyle:breakText',
                        'toggleImageCaption',
                        'ImageResize',
                    ],
                    upload: {
                        types: ['jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg+xml'],
                    },
                },
                codeBlock: {
                    languages: [{
                            language: 'plaintext',
                            label: 'Plain text'
                        },
                        {
                            language: 'c',
                            label: 'C'
                        },
                        {
                            language: 'cs',
                            label: 'C#'
                        },
                        {
                            language: 'cpp',
                            label: 'C++'
                        },
                        {
                            language: 'css',
                            label: 'CSS'
                        },
                        {
                            language: 'diff',
                            label: 'Diff'
                        },
                        {
                            language: 'html',
                            label: 'HTML'
                        },
                        {
                            language: 'java',
                            label: 'Java'
                        },
                        {
                            language: 'javascript',
                            label: 'JavaScript'
                        },
                        {
                            language: 'php',
                            label: 'PHP'
                        },
                        {
                            language: 'python',
                            label: 'Python'
                        },
                        {
                            language: 'ruby',
                            label: 'Ruby'
                        },
                        {
                            language: 'typescript',
                            label: 'TypeScript'
                        },
                        {
                            language: 'xml',
                            label: 'XML'
                        },
                        {
                            language: 'dart',
                            label: 'Dart',
                            class: 'language-dart'
                        },
                    ],
                },
                link: {
                    defaultProtocol: 'http://',
                    decorators: {
                        openInNewTab: {
                            mode: 'manual',
                            label: 'Open in a new tab',
                            attributes: {
                                target: '_blank',
                                rel: 'noopener noreferrer',
                            },
                        },
                    },
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells',
                        'tableCellProperties',
                        'tableProperties',
                    ],
                },
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true,
                    }, ],
                },
                mediaEmbed: {
                    extraProviders: [{
                        name: 'tiktok',
                        url: '^.*https:\\/\\/(?:m|www|vm)?\\.?tiktok\\.com\\/((?:.*\\b(?:(?:usr|v|embed|user|video)\\/|\\?shareId=|\\&item_id=)(\\d+))|\\w+)',
                        html: (match) => {
                            return `<iframe src="https://www.tiktok.com/embed/v2/${match[1]}" width="100%" height="400" frameborder="0"></iframe>`
                        },
                    }, ],
                },
                ...extraConfig,
            }

            // config = ckEditorConfig(config)

            if (editor) {
                ClassicEditor.create(editor, config)
                    .then(async (editor) => {
                        // create function insert html
                        editor.insertHtml = (html) => {
                            const viewFragment = editor.data.processor.toView(html)
                            const modelFragment = editor.data.toModel(viewFragment)
                            editor.model.insertContent(modelFragment)
                        }

                        window.editor = editor

                        // CKEDITOR[element] = editor

                        // const minHeight = $('#' + element).prop('rows') * 90
                        // const className = `ckeditor-${element}-inline`


                        // debounce content for ajax ne
                        let timeout
                        editor.model.document.on('change:data', () => {
                            clearTimeout(timeout)
                            timeout = setTimeout(() => {
                                editor.updateSourceElement()
                            }, 150)
                        })

                        // insert media embed
                        editor.commands._commands.get('mediaEmbed').execute = (url) => {
                            editor.execute('shortcode', `[media url="${url}"][/media]`)
                        }

                        // await this.ckEditorInitialUsing(editor)

                        // await this.ckFinderInitial(editor, element)
                    })
                // .catch((error) => {
                //     console.error(error)
                // })
            }
        }

        document.querySelectorAll('.variationDescription').forEach(item => {
            customCkEditor(item)
        })
    </script>

    @once
        <x-core::custom-template id="gallery_select_image_template">
            <div class="list-photo-hover-overlay">
                <ul class="photo-overlay-actions">
                    <li>
                        <a class="mr10 btn-trigger-edit-gallery-image" data-bs-toggle="tooltip" data-placement="bottom"
                            data-bs-original-title="{{ trans('core/base::base.change_image') }}">
                            <x-core::icon name="ti ti-edit" />
                        </a>
                    </li>
                    <li>
                        <a class="mr10 btn-trigger-remove-gallery-image" data-bs-toggle="tooltip" data-placement="bottom"
                            data-bs-original-title="{{ trans('core/base::base.delete_image') }}">
                            <x-core::icon name="ti ti-trash" />
                        </a>
                    </li>
                </ul>
            </div>
            <div class="custom-image-box image-box">
                <input type="hidden" name="__name__" class="image-data">
                <img src="{{ RvMedia::getDefaultImage() }}" alt="{{ trans('core/base::base.preview_image') }}"
                    class="preview_image">
                <div class="image-box-actions">
                    <a class="btn-images" data-result="images[]" data-action="select-image">
                        {{ trans('core/base::forms.choose_image') }}
                    </a> |
                    <a class="btn_remove_image">
                        <span></span>
                    </a>
                </div>
            </div>
        </x-core::custom-template>
    @endonce
</div>
