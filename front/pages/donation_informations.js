import formValidator from '../validator/formValidator';

export default () => {
    const form = dom('form[name="app_donation"]');

    formValidator('donation-subscription', form);
};
