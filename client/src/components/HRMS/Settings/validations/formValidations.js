import { object, string, number, ref } from "yup";
import i18n from "../../../Shared/i18n/i18n";

// messages
const requiredMessage = i18n.t("formValidationMessages.required");
const emailMessage = i18n.t("formValidationMessages.emailValidationMessage");
const passwordMinMessage = i18n.t("formValidationMessages.passwordMinMessage");
const samePasswordMessage = i18n.t(
  "formValidationMessages.samePasswordMessage"
);

// validation schemas
export const UserProfileSchema = object().shape({
  name: string().required(requiredMessage),
  email: string().email(emailMessage).required(requiredMessage),
  phone: string()
    .required(requiredMessage)
    .typeError("Phone must not be empty"),
  job_title: string().nullable(),
  location: string().nullable(),
  timezone_id: number().required(requiredMessage),
});

export const ChangePasswordSchema = object().shape({
  current_password: string().required(requiredMessage),
  new_password: string().required(requiredMessage).min(6, passwordMinMessage),
  new_password_confirmation: string()
    .min(6, passwordMinMessage)
    .when("new_password", {
      is: (val) => (val && val.length > 0 ? true : false),
      then: string().oneOf([ref("new_password")], samePasswordMessage),
    })
    .required(requiredMessage),
});

export const EmailTemplateSchema = object().shape({
  title: string().required(requiredMessage),
  template_key: string().required(requiredMessage),
});

export const UserSchema = object().shape({
  name: string().required(requiredMessage),
  job_title: string().nullable(),
  location: string().nullable(),
  phone: number().nullable(),
  email: string().email(emailMessage).required(requiredMessage),
  password: string().min(6, passwordMinMessage).required(requiredMessage),
  password_confirmation: string()
    .min(6, passwordMinMessage)
    .required(requiredMessage)
    .when("password", {
      is: (val) => (val && val.length > 0 ? true : false),
      then: string().oneOf([ref("password")], samePasswordMessage),
    }),
  role_id: string().required(requiredMessage),
});

export const UserRolesSchema = object().shape({
  name: string().required(requiredMessage),
});
