import { object, array, string, number, date } from "yup";
import i18n from "../../../Shared/i18n/i18n";

// messages
const requiredMessage = i18n.t("formValidationMessages.required");
const emailMessage = i18n.t("formValidationMessages.emailValidationMessage");
const numberMessage = i18n.t("formValidationMessages.numberValidationMessage");

export const JobValidationSchema = object().shape({
  status: number().required(requiredMessage),
  deadline: date().required(requiredMessage),
  required_candidates: number().required(requiredMessage),
  creator: string().required(requiredMessage),
  contact_name: string().required(requiredMessage),
  contact_email: string().email(emailMessage).required(requiredMessage),
  contact_number: number().typeError(numberMessage).required(requiredMessage),
  training: number().required(requiredMessage),
  observation: number().required(requiredMessage),
  desired_language_comment: object()
    .when(["desired_language"], {
      is: (selectedOption) => selectedOption.includes(undefined),
      then: object().required(requiredMessage),
    })
    .nullable(),
  salary: string().required(requiredMessage),
  salary_type: string().required(requiredMessage),
  salary_amount_1: number().required(requiredMessage),
  salary_amount_2: number()
    .when(["salary_type"], {
      is: (selectedOption) => selectedOption === "Range",
      then: number().required(requiredMessage),
    })
    .nullable(),
  job_type: string().required(requiredMessage),
  job_type_comment: string()
    .when(["job_type"], {
      is: (selectedOption) => selectedOption === "Seasonal Employee",
      then: string().required(requiredMessage),
    })
    .nullable(),
  transport: string().required(requiredMessage),
  transport_comment: string()
    .when(["transport"], {
      is: (selectedOption) => selectedOption === "Yes",
      then: string().required(requiredMessage),
    })
    .nullable(),
  working_hours: string().required(requiredMessage),
  working_hours_comment: string()
    .when(["working_hours"], {
      is: (selectedOption) => selectedOption === "On Schedule",
      then: string().required(requiredMessage),
    })
    .nullable(),
  clothes: string().required(requiredMessage),
  clothes_comment: string()
    .when(["clothes"], {
      is: (selectedOption) => selectedOption === "Yes",
      then: string().required(requiredMessage),
    })
    .nullable(),
  shifts: string().required(requiredMessage),
  shifts_data: array()
    .when(["shifts"], {
      is: (selectedOption) => selectedOption === "Yes",
      then: array().of(
        object().shape({
          start_time: string().required(requiredMessage),
          end_time: string().required(requiredMessage),
        })
      ),
    })
    .nullable(),
  offer_name: string().required(requiredMessage),
  benefits: string().nullable(),
  location: string().nullable(),
  department: string().nullable(),
  description: string().nullable(),
  requirements: string().nullable(),
  comments: string().nullable(),
  additional_information: string().nullable(),
  recess: string().nullable(),
  duration_type: number().required(requiredMessage),
  employment_type: number().required(requiredMessage),
});
