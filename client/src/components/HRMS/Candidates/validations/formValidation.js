import { object, array, string, number, date } from "yup";

// Fields Check
// let matchCharacters = /^[a-z,A-Z,' ',•]+$/;
// let matchCharacters = /^[<>?:;]+$/;
// let matchMixedCharNum = /^[a-zA-Z0-9' ',/]+$/;

// Messages
const requiredMessage = "Kohustuslik"; //Required
const invalidInputMessage = "Vale sisend"; //Invalid input
const personalCodeMessage = "Palun sisesta kehtiv 11-numbriline isikukood"; //Please Enter Valid 11 Digits Personal Code
const emailNotValidMessage = "Email ei ole kehtiv"; //Email Not Valid
const additonalInformationMessage =
  "Lisainfo ei tohi olla pikem kui 255 tähemärki."; //The additonal information may not be greater than 255 characters

// Validations
export const PersonalDataSchema = object().shape({
  first_name: string().required(requiredMessage),
  last_name: string().required(requiredMessage),
  gender: number().positive().integer().min(1).max(2).required(requiredMessage),
  dob: date().nullable().min(new Date(1900, 0, 1)),
  personal_information: string(),
  personal_code: string()
    // .positive()
    // .integer()
    .min(11, personalCodeMessage)
    .max(11, personalCodeMessage),
  phone: number().positive().integer(),
  email: string().email(emailNotValidMessage).required(requiredMessage),
  location: string().nullable(),
  marital_status: string().nullable(),
  children_qty: number().positive().integer().min(0).max(10).nullable(),
  age: number().positive().integer().min(0).max(150),
  source: string().nullable(),
  desired_job: string().nullable(),
  //   job_type: number().positive().integer().nullable(),
  desired_job_time: string().nullable(),
  desired_job_location: string().nullable(),
  desired_salary: number().positive().integer().nullable(),
  action_id: number().positive().integer(),
  status: number().positive().integer().default(0).min(0).max(1),
  // newsletter: number().positive().integer().nullable().default(0).min(0).max(1),
  // consent: number().positive().integer().nullable().default(0).min(0).max(1),
  driving_licenses: array()
    .of(
      object().shape({
        level: string(),
      })
    )
    .nullable(),
  // recommendations: array().of(
  //   string().typeError(invalidInputMessage).nullable()
  // ),
  keywords: array().of(string().nullable()),
});

export const JobHistorySchema = object().shape({
  jobs: array().of(
    object().shape({
      company_name: string().required(requiredMessage),
      designation: string().required(requiredMessage),
      work_place: string().nullable(),
      description: string().nullable(),
    })
  ),
});

export const EducationValidationSchema = object().shape({
  education: array().of(
    object().shape({
      institute: string().required(requiredMessage),
      speciality: string().nullable(),
      additonal_information: string().max(255, additonalInformationMessage),
    })
  ),
});

export const AdditionalCoursesValidationSchema = object().shape({
  // candidatecv_id: number().required(requiredMessage),
  courses: array().of(
    object().shape({
      title: string().nullable(),
      description: string().required(requiredMessage),
      total_hours: number().nullable(),
    })
  ),
});

export const LanguagesValidationSchema = object().shape({
  language: array().of(
    object().shape({
      name: string().required(requiredMessage),
      speaking: string().required(requiredMessage),
      reading_writing: string().required(requiredMessage),
    })
  ),
});

export const AgreementsValidationSchema = object().shape({
  user_pr_id: number().required(requiredMessage).positive().integer(),
});
