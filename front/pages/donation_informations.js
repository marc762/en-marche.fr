import formValidator from '../validator/formValidator';

export default () => {
    const form = dom('[name="app_donation"]');

    formValidator('AppBundle\\Form\\DonationRequestType', form);
};
