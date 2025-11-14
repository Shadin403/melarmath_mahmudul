<div id="product-variations-wrapper">
    {!! $productVariationTable->renderTable() !!}
</div>

<x-core::modal id="select-attribute-sets-modal" :title="trans('plugins/ecommerce::products.select_attribute')">
    @include('plugins/ecommerce::products.partials.attribute-sets', compact('productAttributeSets'))

    <x-slot:footer>
        <x-core::button type="button" data-bs-dismiss="modal">
            {{ trans('core/base::base.close') }}
        </x-core::button>

        <x-core::button type="button" color="primary" id="store-related-attributes-button" class="ms-auto">
            {{ trans('plugins/ecommerce::products.save_changes') }}
        </x-core::button>
    </x-slot:footer>
</x-core::modal>

@push('footer')
    <x-core::modal id="add-new-product-variation-modal" :title="trans('plugins/ecommerce::products.add_new_variation')" size="xl">
        <x-core::loading />
        <x-slot:footer>
            <x-core::button type="button" data-bs-dismiss="modal">
                {{ trans('core/base::base.close') }}
            </x-core::button>

            <x-core::button type="button" color="primary" id="store-product-variation-button" class="ms-auto">
                {{ trans('plugins/ecommerce::products.save_changes') }}
            </x-core::button>
        </x-slot:footer>
    </x-core::modal>

    <x-core::modal id="edit-product-variation-modal" :title="trans('plugins/ecommerce::products.edit_variation')" size="xl">
        <x-core::loading />
        <x-slot:footer>
            <x-core::button type="button" data-bs-dismiss="modal">
                {{ trans('core/base::base.close') }}
            </x-core::button>

            <x-core::button type="button" color="primary" id="update-product-variation-button" class="ms-auto">
                {{ trans('plugins/ecommerce::products.save_changes') }}
            </x-core::button>
        </x-slot:footer>
    </x-core::modal>

    <x-core::modal id="generate-versions-modal" :title="trans('plugins/ecommerce::products.generate_variations')" :description="trans('plugins/ecommerce::products.generate_variations_confirmation')">
        <x-core::form.label>
            {{ trans('plugins/ecommerce::products.select_attributes') }}
        </x-core::form.label>
        <div class="row row-cols-2">
            @foreach ($productAttributeSets as $productAttributeSet)
                @continue(!$productAttributeSet->is_selected)
                <div class="col">
                    <h4 class="my-2">{{ $productAttributeSet->title }}</h4>

                    <fieldset class="form-fieldset">
                        <div data-bb-toggle="tree-checkboxes">
                            <ul class="list-unstyled">
                                <li>
                                    <x-core::form.checkbox :label="trans('plugins/ecommerce::products.all')" :checked="true" />

                                    <ul class="list-unstyled ms-2 mt-2">
                                        @foreach ($productAttributeSet->attributes as $attribute)
                                            <li>
                                                <x-core::form.checkbox :label="$attribute->title" name="attributes[]"
                                                    value="{{ $attribute->id }}" :checked="true" />
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </fieldset>
                </div>
            @endforeach
        </div>
        <x-slot:footer>
            <x-core::button type="button" data-bs-dismiss="modal">
                {{ trans('core/base::base.close') }}
            </x-core::button>

            <x-core::button type="button" color="primary" data-bb-toggle="generate-versions-button" class="ms-auto">
                {{ trans('plugins/ecommerce::products.continue') }}
            </x-core::button>
        </x-slot:footer>
    </x-core::modal>

    <x-core::modal.action id="confirm-delete-version-modal" type="danger" :title="trans('plugins/ecommerce::products.delete_variation')" :description="trans('plugins/ecommerce::products.delete_variation_confirmation')"
        :submit-button-attrs="['id' => 'delete-version-button']" :submit-button-label="trans('plugins/ecommerce::products.continue')" />

    <x-core::modal.action id="delete-variations-modal" type="danger" :title="trans('plugins/ecommerce::products.delete_variations')" :description="trans('plugins/ecommerce::products.delete_variations_confirmation')"
        :submit-button-attrs="['id' => 'delete-selected-variations-button']" :submit-button-label="trans('plugins/ecommerce::products.continue')" />

    <!-- Variation Translation Modal -->
    <x-core::modal id="variation-translation-modal" :title="trans('plugins/ecommerce::products.edit_variations_for', ['name' => $product->name])" size="xl">
        <form id="variation-translation-form" method="POST"
            action="{{ route('products.variations.update', $product->getKey()) }}" data-ajax="true">
            @csrf
            <input type="hidden" id="modal-variation-id" name="variation_id" value="">
            <div class="modal-body">
                <div id="variation-translation-content" class="variation-translation-content">
                    @php
                        // Check if a specific variation ID is requested
                        $requestedVariationId = request('variation_id') ?: null;

                        if ($requestedVariationId) {
                            $variations = \Botble\Ecommerce\Models\ProductVariation::query()
                                ->where('id', $requestedVariationId)
                                ->where('configurable_product_id', $product->getKey())
                                ->with('translations')
                                ->get();
                        } else {
                            $variations = \Botble\Ecommerce\Models\ProductVariation::query()
                                ->where('configurable_product_id', $product->getKey())
                                ->with('translations')
                                ->get();
                        }

                        $languages = \Botble\Language\Facades\Language::getActiveLanguage([
                            'lang_code',
                            'lang_name',
                            'lang_flag',
                        ]);
                        $defaultLocale = \Botble\Language\Facades\Language::getDefaultLocaleCode();
                    @endphp

                    @if ($variations->isEmpty())
                        <div class="alert alert-info">
                            {{ trans('plugins/ecommerce::products.no_variations_found') }}
                        </div>
                    @else
                        @foreach ($variations as $variation)
                            <div class="variation-item mb-4 p-3 border rounded">
                                <h6 class="variation-title">
                                    {{ trans('plugins/ecommerce::products.variation') }} #{{ $variation->id }}
                                    @if ($variation->is_default)
                                        <span
                                            class="badge badge-primary">{{ trans('plugins/ecommerce::products.default') }}</span>
                                    @endif
                                </h6>

                                @if ($languages->count() > 1)
                                    <div class="language-translations mt-3">
                                        <h6>{{ trans('plugins/language::language.translations') }}</h6>

                                        <ul class="nav nav-tabs" role="tablist">
                                            @foreach ($languages as $language)
                                                @if ($language->lang_code !== $defaultLocale)
                                                    <li class="nav-item">
                                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                            id="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}-tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#modal-variation-{{ $variation->id }}-{{ $language->lang_code }}"
                                                            type="button" role="tab"
                                                            aria-controls="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}"
                                                            {{ $loop->first ? 'aria-selected="true"' : 'aria-selected="false"' }}>
                                                            <span
                                                                class="flag-icon flag-icon-{{ strtolower($language->lang_flag) }}"></span>
                                                            {{ $language->lang_name }}
                                                        </button>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>

                                        <div class="tab-content mt-3">
                                            @foreach ($languages as $language)
                                                @if ($language->lang_code !== $defaultLocale)
                                                    @php
                                                        $translation = $variation->translations
                                                            ->where('lang_code', $language->lang_code)
                                                            ->first();
                                                    @endphp
                                                    <div class="tab-pane {{ $loop->first ? 'active show' : '' }}"
                                                        id="modal-variation-{{ $variation->id }}-{{ $language->lang_code }}">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>{{ trans('plugins/ecommerce::products.variation_title') }}
                                                                        ({{ $language->lang_name }})
                                                                    </label>
                                                                    <input type="text"
                                                                        name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_title]"
                                                                        value="{{ $translation ? $translation->variation_title : '' }}"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>{{ trans('plugins/ecommerce::products.variation_description') }}
                                                                        ({{ $language->lang_name }})</label>
                                                                    <textarea name="variations[{{ $variation->id }}][translations][{{ $language->lang_code }}][variation_desc]"
                                                                        class="form-control variationDescription" rows="3">{{ $translation ? $translation->variation_desc : '' }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <x-slot:footer>
                <x-core::button type="button" data-bs-dismiss="modal">
                    {{ trans('core/base::base.close') }}
                </x-core::button>

                <x-core::button type="submit" color="primary" class="ms-auto">
                    {{ trans('core/base::forms.save') }}
                </x-core::button>
            </x-slot:footer>
        </form>
    </x-core::modal>

    <script>
        function customCkEditor(editor, extraConfig) {
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

            if (editor) {
                ClassicEditor.create(editor, config)
                    .then(async (editor) => {
                        editor.insertHtml = (html) => {
                            const viewFragment = editor.data.processor.toView(html)
                            const modelFragment = editor.data.toModel(viewFragment)
                            editor.model.insertContent(modelFragment)
                        }

                        window.editor = editor

                        let timeout
                        editor.model.document.on('change:data', () => {
                            clearTimeout(timeout)
                            timeout = setTimeout(() => {
                                editor.updateSourceElement()
                            }, 150)
                        })

                        editor.commands._commands.get('mediaEmbed').execute = (url) => {
                            editor.execute('shortcode', `[media url="${url}"][/media]`)
                        }
                    })
            }
        }

        // Function to apply modal triggers to language buttons
        function applyModalTriggers() {
            $('#product-variations-wrapper table tbody tr').each(function() {
                var row = $(this);
                var variationId = row.find('input[type="checkbox"][name*="id"]').val() ||
                    row.find('td:first input[type="checkbox"]').val() ||
                    row.data('id');

                row.find('td a[href*="ref_lang"]').each(function() {
                    var href = $(this).attr('href');
                    if (href && href.indexOf('?ref_lang=') > -1 && !$(this).hasClass(
                            'btn-variation-translation')) {
                        $(this).attr('href', '#');
                        $(this).removeAttr('data-bs-toggle');
                        $(this).removeAttr('data-bs-target');
                        $(this).attr('data-variation-id', variationId);
                        $(this).addClass('btn-variation-translation');
                        $(this).removeAttr('data-bs-original-title');
                    }
                });
            });
        }

        $(document).ready(function() {
            // Handle clicks on variation translation buttons
            $(document).on('click', '.btn-variation-translation', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var variationId = $(this).data('variation-id');
                if (variationId) {
                    // Show loading state in content area
                    $('#variation-translation-content').html(
                        '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
                    );

                    // Load specific variation content via AJAX
                    $.ajax({
                        url: '{{ route('products.variations.edit', ':productId') }}'.replace(
                            ':productId', '{{ $product->getKey() }}'),
                        method: 'GET',
                        data: {
                            variation_id: variationId
                        },
                        success: function(response) {
                            // Update only the content div, keeping the form structure
                            $('#variation-translation-content').html(response);
                            // Show the modal
                            $('#variation-translation-modal').modal('show');
                            // Initialize CKEditor for loaded content
                            $('#variation-translation-modal .variationDescription').each(
                                function() {
                                    if (!$(this).hasClass('ck-editor-initialized')) {
                                        $(this).addClass('ck-editor-initialized');
                                        customCkEditor(this);
                                    }
                                });
                        },
                        error: function(xhr) {
                            $('#variation-translation-modal .modal-body').html(
                                '<div class="alert alert-danger">Failed to load translation form</div>'
                            );
                        }
                    });
                }
            });

            // Apply modal triggers initially
            applyModalTriggers();

            // Re-apply modal triggers whenever table content changes
            $('#product-variations-wrapper').on('DOMSubtreeModified', 'table', function() {
                setTimeout(applyModalTriggers, 100); // Small delay to ensure DOM is ready
            });

            // Also apply triggers after any AJAX operations that might affect the table
            $(document).on('ajaxComplete', function() {
                setTimeout(applyModalTriggers, 200);
            });

            // Initialize modal tabs and CKEditor when modal is shown
            $(document).on('shown.bs.modal', '#variation-translation-modal', function() {
                // Activate first tab if not already active
                var firstTab = $('#variation-translation-modal .nav-tabs .nav-link').first();
                if (!firstTab.hasClass('active')) {
                    firstTab.tab('show');
                }

                // Initialize CKEditor for visible description textareas
                $('#variation-translation-modal .variationDescription').each(function() {
                    if (!$(this).hasClass('ck-editor-initialized')) {
                        $(this).addClass('ck-editor-initialized');
                        customCkEditor(this);
                    }
                });
            });

            // Handle AJAX form submission for variation translation modal
            $(document).on('submit', '#variation-translation-form', function(e) {
                e.preventDefault();
                console.log('Form submitted'); // Debug log

                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        console.log('AJAX success:', response); // Debug log
                        if (response.error) {
                            Botble.showError(response.message);
                        } else {
                            Botble.showSuccess(response.message);
                            $('#variation-translation-modal').modal('hide');
                            // Reload the variations table and re-apply modal triggers
                            if (typeof window.LaravelDataTables !== 'undefined' &&
                                window.LaravelDataTables['product-variations-table']) {
                                window.LaravelDataTables['product-variations-table'].ajax
                                    .reload(function() {
                                        console.log(
                                            'Table reloaded, re-applying modal triggers'
                                        );
                                        // Re-apply modal triggers after table reload
                                        applyModalTriggers();
                                    });
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX error:', xhr); // Debug log
                        Botble.handleError(xhr);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Also handle submit button click as backup
            $(document).on('click', '#variation-translation-modal button[type="submit"]', function(e) {
                e.preventDefault();
                console.log('Submit button clicked'); // Debug log
                $('#variation-translation-form').trigger('submit');
            });

            // Close modal when main product form is submitted to prevent conflicts
            $(document).on('submit', 'form[action*="/products/"]', function(e) {
                if ($(this).attr('id') !== 'variation-translation-form') {
                    $('#variation-translation-modal').modal('hide');
                }
            });

            // Also close modal on any page navigation or AJAX success that might reload content
            $(document).on('ajaxSuccess', function(event, xhr, settings) {
                if (settings.url && (settings.url.includes('/products/') || settings.url.includes(
                        'products'))) {
                    if (!$('#variation-translation-modal').hasClass('show')) {
                        // Modal is not visible, no action needed
                        return;
                    }
                    // If it's a product-related AJAX call and modal is open, close it
                    if (!settings.url.includes('variations/update')) {
                        $('#variation-translation-modal').modal('hide');
                    }
                }
            });
        });
    </script>
@endpush
