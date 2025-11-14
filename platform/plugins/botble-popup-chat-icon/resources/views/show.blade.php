<div id="button-contact-vr" class="d-sm-block">
    <div id="gom-all-in-one" style="margin-block-end: 30px;">
        @if ($facebook = theme_option('chat_btn_facebook'))
            <div id="fanpage-vr" class="button-contact">
                <a target="_blank" href="{{ $facebook }}">
                    <div class="phone-vr">
                        <div class="phone-vr-circle-fill"></div>
                        <div class="phone-vr-img-circle">
                            <img data-bb-lazy="true" width="200" height="200" loading="lazy"
                                src="/vendor/core/plugins/popup-chat/images/Facebook.png" alt="{{ $facebook }}">
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if ($messenger = theme_option('chat_btn_messenger'))
            <div id="messenger-vr" class="button-contact">
                <a target="_blank" href="{{ $messenger }}">
                    <div class="phone-vr">
                        <div class="phone-vr-circle-fill" style="background: #1876f27c ! important;"></div>
                        <div class="phone-vr-img-circle" style="background: #e1e8f1 ! important;">
                            <img data-bb-lazy="true" width="200" height="200" loading="lazy"
                                src="/vendor/core/plugins/popup-chat/images/messenger-2.png" alt="{{ $messenger }}">
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if ($youtube = theme_option('chat_btn_youtube'))
            <div id="youtube-vr" class="button-contact">
                <a target="_blank" href="{{ $youtube }}">
                    <div class="phone-vr">
                        <div class="phone-vr-circle-fill" style="background: #ffffff ! important;"></div>
                        <div class="phone-vr-img-circle" style="background: #ffffff ! important;">
                            <img data-bb-lazy="true" width="200" height="200" loading="lazy"
                                src="/vendor/core/plugins/popup-chat/images/youtube.png" alt="{{ $youtube }}">
                        </div>
                    </div>
                </a>
            </div>
        @endif


        @if ($whatsapp = theme_option('chat_btn_whatsapp'))
            <div id="whatsapp-vr" class="button-contact">
                <a target="_blank" href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}">
                    <div class="phone-vr">
                        <div class="phone-vr-circle-fill" style="background: #e1e8f1 ! important;"></div>
                        <div class="phone-vr-img-circle">
                            <img data-bb-lazy="true" width="200" height="200" loading="lazy"
                                src="/vendor/core/plugins/popup-chat/images/whatsapp.png" alt="{{ $whatsapp }}">
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if ($hotline = theme_option('hotline'))
            <div id="phone-vr" class="button-contact">
                <a href="tel:{{ $hotline }}">
                    <div class="phone-vr">
                        <div class="phone-vr-circle-fill"></div>
                        <div class="phone-vr-img-circle">
                            <img data-bb-lazy="true" width="200" height="200" loading="lazy"
                                src="/vendor/core/plugins/popup-chat/images/phone.png" alt="{{ $hotline }}">
                        </div>
                    </div>
                </a>
            </div>
            <div class="phone-bar phone-bar-n">
                <a href="tel:{{ $hotline }}">
                    <span class="text-phone">{{ $hotline }}</span>
                </a>
            </div>
        @endif
    </div>
</div>
