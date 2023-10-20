import { object, date } from "yup";
import i18n from "../../../Shared/i18n/i18n";

// messages
const requiredMessage = i18n.t("formValidationMessages.required");

// validation schemas
export const TaskValidationSchema = object().shape({
  // description: string().required(requiredMessage),
  deadline: date().required(requiredMessage),
});
