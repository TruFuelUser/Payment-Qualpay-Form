(function( $ ) {
	'use strict';
    const lang = TFContent.language;

    function contentButtons(data){
        const $btnNext   = $('.btn-action.next');
        const $btnBack   = $('.btn-action.back');
        const $btnStart  = $('.btn-action.next.start');
        const $btnSubmit = $('button[type="submit"]'); 

        const buttonsJson = data.buttons;

        $btnNext.html(buttonsJson.nextButton.html);
        $btnBack.html(buttonsJson.backButton.html);
        $btnStart.html(buttonsJson.startButton.html);
        $btnSubmit.html(buttonsJson.submitButton.html);

    }

    function contentDescriptionForm(data){
        const section = $('#DescriptionForm');
        const $h2 = section.find('h2');
        const $paragraph = section.find('.TextFormDescription p');

        const sectionJson = data.descriptionForm;

        $h2.html(sectionJson.h2.html);
        $paragraph.html(sectionJson.paragraph.html);
    }

    function contentContactInfo(data){
        const section = $('#ContactInfo');
        const sectionJson = data.contactInfo;

        // titles and text
        const $h3 = section.find('h3');
        const $h2 = section.find('h2');

        // Inputs    
        const $inputFirstName     = $('#FirstNameSection input');
        const $inputLastName      = $('#LastNameSection input');
        const $inputPhoneType     = $('#PhoneTypeSection input');
        const $inputPhoneSelected = $('#PhoneSelected');
        const $inputEmail         = $('#EmailSection input');

        // Assign HTML and Values
        $h3.html(sectionJson.h3.html);
        $h2.html(sectionJson.h2.html);

        $inputFirstName.attr('placeholder', `${sectionJson.firstName.placeholder}`);
        $inputLastName.attr('placeholder', `${sectionJson.lastName.placeholder}`);
        $inputPhoneType.attr('placeholder', `${sectionJson.phoneType.placeholder}`);

        $inputPhoneSelected.attr('data-required-message', `${sectionJson.phoneSelected.dataRequiredMessage}`);

        $inputEmail.attr('placeholder', `${sectionJson.email.placeholder}`);
        $inputEmail.attr('data-required-message', `${sectionJson.email.dataRequiredMessage}`);
    }

    function contentCompanyInfo(data) {
        const section = $('#CompanyInfo');
        const sectionJson = data.companyInfo;

        // titles and text
        const $h3 = section.find('h3');
        const $h2 = section.find('h2');
        // Label
        const $labelSoftware  = $('#AppSection').find('label');

        // Inputs    
        const $inputCompany   = $('#CompanySection').find('input');
        const $inputCountry   = $('#CountrySection').find('input');
        const $optionCountry  = $('#CountrySection').find('select').find('option:first');
        const $inputState     = $('#StateSection').find('input');
        const $optionState    = $('#StateSection').find('select').find('option:first');
        const $inputCity      = $('#CitySection').find('input');
        const $inputZip       = $('#ZipSection').find('input');
        const $inputAddress   = $('#AddressSection').find('input');
        const $inputSoftware  = $('#App');

        // Assign HTML and Values
        $h3.html(sectionJson.h3.html);
        $h2.html(sectionJson.h2.html);

        $inputCompany.attr('placeholder', `${sectionJson.companyName.placeholder}`);
        $inputCountry.attr('placeholder', `${sectionJson.country.placeholder}`);
        $optionCountry.html(sectionJson.country.selectTextEmpty);
        $inputState.attr('placeholder', `${sectionJson.state.placeholder}`);
        $optionState.html(sectionJson.state.selectTextEmpty);
        $inputCity.attr('placeholder', `${sectionJson.city.placeholder}`);     
        $inputZip.attr('placeholder', `${sectionJson.zip.placeholder}`);
        $inputAddress.attr('placeholder', `${sectionJson.address.placeholder}`);
        $labelSoftware.html(sectionJson.software.title);
        $inputSoftware.attr('placeholder', `${sectionJson.software.placeholder}`);
    }

    function contentReferralInfo(data) {
        const section = $('#ReferralInfo');
        const sectionJson = data.referralInfo;
        const $referencesSection    = $('#ReferencesSection');

        // titles and text
        const $h3 = section.find('h3');
        const $h2 = section.find('h2');

        // Assign HTML and Values
        const $label                = $referencesSection.find('label');
        const $inputOtherReferences = $('#ReferencesNotesSection').find('input');

        $h3.html(sectionJson.h3.html);
        $h2.html(sectionJson.h2.html);
        $label.html(sectionJson.reference.title);
        $inputOtherReferences.attr('placeholder', `${sectionJson.reference.placeholder}`);
    }

    function contentMessageInfo(data) {
        const section = $('#MessageInfo');
        const sectionJson = data.messageInfo;

        // titles and text
        const $h3 = section.find('h3');
        const $h2 = section.find('h2');

        // input(s)
        const $inputMessage = $('#Message');

        // Assign HTML and Values
        $h3.html(sectionJson.h3.html);
        $h2.html(sectionJson.h2.html);
        $inputMessage.attr('placeholder', `${sectionJson.message.placeholder}`);
    }

    function contentEndForm(data) {
        const section = $('#EndForm');
        const sectionJson = data.endForm;

        // titles, links and text
        const $h2 = section.find('h2');
        const $returnMain = $('.ReturnMain');
        const $paragraph = section.find('.TextFormDescription p');

        // Assign HTML and Values
        $h2.html(sectionJson.h2.html);
        $returnMain.html(sectionJson.returnMain.html);
        $paragraph.html(sectionJson.paragraph.html);
    }


    async function loadContentData() {
        try {
            const response = await fetch(`${TFContent.contentBase}-${lang}.json`);
            const data = await response.json();

            contentButtons(data);
            contentDescriptionForm(data);
            contentContactInfo(data);
            contentCompanyInfo(data);
            contentReferralInfo(data);
            contentMessageInfo(data);
            contentEndForm(data);

        } catch (error) {
            console.log('Error find content file: '+error)
        }

    }

    loadContentData();

})( jQuery );
