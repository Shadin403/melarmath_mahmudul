<section class="pt-100 pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 m-auto">
                <div class="login_wrap widget-taber-content p-30 background-white border-radius-10">
                    <div class="padding_eight_all bg-white">
                        <div class="heading_s1 mb-20 text-center">
                            <h3 class="mb-20">{{ __('Order tracking') }}</h3>
                            <p>{{ __('Tracking your order status') }}</p>
                        </div>

                        <div class="mb-30">
                            {!! $form->modify(
                                    'submit',
                                    'button',
                                    [
                                        'label' => __('Find'),
                                        'attr' => [
                                            'type' => 'submit',
                                            'class' => 'w-100 btn btn-primary',
                                        ],
                                    ],
                                    true,
                                )->renderForm() !!}


                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const orderIdField = document.querySelector('input[name="order_id"]');
                                    const emailField = document.querySelector('input[name="email"]');

                                    const url = new URL(window.location.href);
                                    const orderIdFromQuery = url.searchParams.get('order_id');
                                    const hash = url.hash;

                                    // যদি order_id query-তে খালি থাকে কিন্তু URL-এ হ্যাশ থাকে
                                    if ((orderIdFromQuery === '' || orderIdFromQuery === null) && hash) {
                                        // হ্যাশ থেকে '#' চিহ্নটি বাদ দিন
                                        const hashContent = hash.substring(1);

                                        // হ্যাশের অংশটিকে query string-এর মতো করে পার্স করুন
                                        const hashParams = new URLSearchParams(hashContent);

                                        let finalOrderId = null;

                                        if (hashParams.has('order_id')) {
                                            // যদি হ্যাশটি #order_id=... ফরম্যাটে থাকে
                                            finalOrderId = hashParams.get('order_id');
                                        } else {
                                            // যদি হ্যাশটি শুধু #kg-10000757... ফরম্যাটে থাকে
                                            // '&' চিহ্নের আগের অংশটিকে order_id হিসেবে ধরে নিই
                                            finalOrderId = hashContent.split('&')[0];
                                        }

                                        if (finalOrderId && orderIdField) {
                                            // ইনপুট ফিল্ডে মানটি বসিয়ে দিন
                                            orderIdField.value = finalOrderId;

                                            // URL ঠিক করার জন্য নতুন URL তৈরি করুন
                                            const newUrl = new URL(window.location);
                                            newUrl.searchParams.set('order_id', finalOrderId);

                                            // হ্যাশ থেকে ইমেইল খুঁজে বের করে query-তে যোগ করুন
                                            if (hashParams.has('email') && emailField) {
                                                const emailFromHash = hashParams.get('email');
                                                emailField.value = emailFromHash;
                                                newUrl.searchParams.set('email', emailFromHash);
                                            }

                                            // URL থেকে হ্যাশ মুছে দিন
                                            newUrl.hash = '';

                                            // ব্রাউজারের হিস্টোরিতে পেজ রিলোড ছাড়াই URL আপডেট করুন
                                            window.history.replaceState({}, '', newUrl.toString());
                                        }
                                    }

                                    // পেজ লোড হওয়ার পর ৫০% স্ক্রল করে মাঝখানে নিয়ে যান
                                    setTimeout(function() {
                                        const pageHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
                                        const scrollToPosition = Math.round(pageHeight * 0.2); // 2০% অবস্থানে স্ক্রল করুন
                                        window.scrollTo({
                                            top: scrollToPosition,
                                            behavior: 'smooth' // স্মুথ স্ক্রলিং
                                        });
                                    }, 500); // ৫০০ms দেরি দিয়ে স্ক্রল করুন যাতে পেজ পুরোপুরি লোড হয়
                                });
                            </script>
                            @if (request()->query('order_id') && request()->query('email'))
                                <a href="{{ Route('downloadInvoiceByOrderCode', [request()->query('order_id')]) }}"
                                    class="mt-2 btn btn-primary" style="width: 100%;">{{ __('Download Invoice') }}</a>
                            @endif

                        </div>

                        <div style="margin-top: 60px;">
                            @include(EcommerceHelper::viewPath('includes.order-tracking-detail'))
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
