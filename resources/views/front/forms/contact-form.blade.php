<form action="{{ route('contact') }}" class="row col-12" method="POST" id="contact-form">
    @csrf
    <div class="px-3 messages">
        {{ session('status') }}
    </div>
    {{-- @include('layouts.errors-and-messages') --}}
    <div class="modal-body col-lg-8 offset-lg-2">
        <div class="col-12 pb-2">
            <label class="col-12 px-0" for="modal--contact_name">Reason for Contact</label>
            <select name="contact_type" class="form-control col-4 border-radius-0" id="contact_type">
                <option value="General Contact" {{ array_key_exists('contact_type', request()->query()) && request()->query()['contact_type'] === 'general-contact' ? 'selected="selected"' : '' }}>General Contact</option>
                <option value="Instant Quote" {{ array_key_exists('contact_type', request()->query()) && request()->query()['contact_type'] === 'instant-quote' ? 'selected="selected"' : '' }}>Instant Quote Request</option>
                <option value="Service Request" {{ array_key_exists('contact_type', request()->query()) && request()->query()['contact_type'] === 'service-request' ? 'selected="selected"' : '' }}>Service Request</option>
                <option value="Customer Support" {{ array_key_exists('contact_type', request()->query()) && request()->query()['contact_type'] === 'customer-support' ? 'selected="selected"' : '' }}>Customer Support</option>
            </select>
            @if ($errors->has('contact_type'))
                <div class="col-12 py-1 px-0 text-red font-weight-bold">
                    {{ $errors->first('contact_type') }}
                </div>
            @endif
        </div>
        <div class="col-12 col-md-8 pb-2 relative">
            <label class="col-12 px-0" for="modal--contact_name">Name</label>
            <input class="col-12 form-control modal--contact-us__input--name" type="text" name="contact_name" id="modal--contact_name">                
            @if ($errors->has('contact_name'))
                <div class="col-12 py-1 px-0 text-red font-weight-bold">
                    {{ $errors->first('contact_name') }}
                </div>
            @endif
        </div>
        <div class="col-12 col-md-8 pb-2 relative">
            <label class="col-12 px-0" for="modal--contact_email">Email</label>
            <input class="col-12 form-control modal--contact-us__input--email" type="email" name="contact_email" id="modal--contact_email">
            @if ($errors->has('contact_email'))
                <div class="col-12 py-1 px-0 text-red font-weight-bold">
                    {{ $errors->first('contact_email') }}
                </div>
            @endif
        </div>
        <div class="col-md-8 pb-2 relative">
            <label class="col-12 px-0" for="modal--contact_phone">Phone Number</label>
            <input class="col-12 form-control modal--contact-us__input--phone" type="phone" name="contact_phone" id="modal--contact_phone">
            @if ($errors->has('contact_phone'))
                <div class="col-12 py-1 px-0 text-red font-weight-bold">
                    {{ $errors->first('contact_phone') }}
                </div>
            @endif

        </div>
        <div class="col-12 col-md-8 pb-2 relative">
            <label class="col-12 px-0" for="modal--contact_message">Message</label>
            <textarea class="col-12 form-control modal--contact-us__input--message" name="contact_message" id="modal--contact_message"></textarea>
            @if ($errors->has('contact_message'))
                <div class="col-12 py-1 px-0 text-red font-weight-bold">
                    {{ $errors->first('contact_message') }}
                </div>
            @endif
        </div>
        <div class="col-12 mb-3">
            <button
                type="submit"
                class="g-recaptcha modal--contact-us__button link btn btn-highlight border-radius-0 text-white px-3 py-2"
                data-sitekey="{{ config('recaptcha.key') }}"
                data-callback='onSubmit'
                data-action='submit'
            >Submit</button>
        </div>
    </div>
</form>

@section('js')
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        function onSubmit(token) {
          document.getElementById("contact-form").submit();
        }

        if(window.location.hash === '#instant-quote') {
            document.getElementById('contact_type').value = 'Instant Quote';
        }
    </script>
@endsection