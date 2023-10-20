import { object, array, string, number } from "yup";

// Messages
const requiredMessage = "Kohustuslik"; //Required
const locationMessages = `Palun lisa vähemalt üks asukoht`; //Provide At Least One Location
const industryMessages = `Palun lisa vähemalt üks tegevusala`; //Required! Provide At Least One Industry
const emailMessage = "Peab olema kehtiv email"; //Must Be A Valid Email
const validNumberMessage = "Peab olema kehtiv number"; //Must be a valid number

export const CompanyValidationSchema = object().shape({
  invoicing_info: number()
    .typeError(validNumberMessage)
    .required(requiredMessage),
  vat: string().nullable(),
  en: object().shape({
    company_name: string(),
    contacts: array().of(
      object().shape({
        name: string().required(requiredMessage),
        email: string().email(emailMessage).required(requiredMessage),
        phone: number().typeError(validNumberMessage).required(requiredMessage),
        position: string().nullable(),
      })
    ),
    location: array().of(
      string().min(1, locationMessages).required(requiredMessage)
    ),
  }),
  industry_names: array().of(
    string().min(1, industryMessages).required(requiredMessage)
  ),
});

export const EditCompanyValidationSchema = object().shape({
  invoicing_info: number()
    .typeError(validNumberMessage)
    .required(requiredMessage),
  vat: string().nullable(),
  en: object().shape({
    company_name: string(),
    contacts: array().of(
      object().shape({
        name: string().required(requiredMessage),
        email: string().email(emailMessage).required(requiredMessage),
        phone: number().typeError(validNumberMessage).required(requiredMessage),
        position: string().nullable(),
      })
    ),
    location: array().of(
      object().shape({
        // id: number().required(),
        location: string().min(1, locationMessages).required(requiredMessage),
      })
    ),
  }),
  industry_names: array().of(
    string().min(1, industryMessages).required(requiredMessage)
  ),
});
