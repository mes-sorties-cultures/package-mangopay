<form action="{{$cardRegistrationUrl}}" method="post">
    <input type="hidden"
           name="data"
           value="{{$preRegistrationData}}">
    <input type="hidden"
           name="accessKeyRef"
           value="{{$accessKey}}">
    <input type="hidden"
           name="returnURL"
           value="{{$returnUrl}}">

    <div class="col-xs-12 mbottom15">
        <label class="col-xs-12 col-sm-6 col-md-4 mbottom10" for="cc-number">
            {{__('mangopay.form.creditCardNumber')}} :
        </label>
        <div class="col-xs-12 col-sm-6 col-md-8">
            <input type="text"
                   name="cardNumber"
                   class="cc-number"
                   placeholder="•••• •••• •••• ••••"
                   required>
            <span class="cc-brand"></span>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 mbottom15">
        <label class="col-xs-12 col-sm-8 mbottom10" for="cc-cvc">
            {{__('mangopay.form.cvv')}} :
        </label>
        <div class="col-xs-12 col-sm-4">
            <input type="text"
                   name="cardCvx"
                   class="cc-cvc"
                   placeholder="•••"
                   required>
        </div>
    </div>

    <div class="col-xs-12 col-sm-6 mbottom15">
        <label class="col-xs-12 col-sm-7 mbottom10" for="cc-exp">
            {{__('mangopay.form.expiry_date')}} :
        </label>
        <div class="col-xs-12 col-sm-5">
            <input type="text"
                   name="cardCvx"
                   class="cc-exp"
                   placeholder="•• / ••"
                   required>
        </div>
    </div>

    <label class="col-xs-12 mbottom15">
        <input type="checkbox"
               name="cgu"
               required>
        {{__('mangopay.form.cgu')}}
    </label>

    <div class="row tcenter clearfix wfull">
        <input type="submit" value="{{__('mangopay.form.submit')}}">
    </div>
</form>