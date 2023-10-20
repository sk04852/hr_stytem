import { object, array, string, number, date, boolean } from "yup";
import i18n from "../../../Shared/i18n/i18n";

// messages
const requiredMessage = i18n.t("formValidationMessages.required");

// Validations
export const EventValidationSchema = object().shape({
  name: string().required(requiredMessage),
  description: string().required(requiredMessage),
  is_all_day: boolean().default(true),
  start_date_time: date().required(requiredMessage),
  end_date_time: date().required(requiredMessage),
  transparency: string().required(requiredMessage),
});

export const CalendarValidationSchema = object().shape({
  title: string().required(requiredMessage),
});
