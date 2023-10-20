import { combineReducers } from "redux";
import settings from "./settings";
import candidateActions from "./candidateActionsReducers";
import candidates from "./candidatesReducers";
import companies from "./companiesReducers";
import jobs from "./jobsReducers";
import UserReducer from "./usersReducer";
import languages from "./languageReducer";
import tags from "./tagsReducer";
import skills from "./skillsReducer";
import roles from "./rolesReducer";
import permissions from "./permissionsReducers";
import timezone from "./timezoneReducer";
import jobTypes from "./jobTypesReducer";
import status from "./statusReducer";
import educationDegree from "./educationReducer";
import emailTemplates from "./emailTemplatesReducers";
import sendEmail from "./emailSendingReducer";
import importFromFile from "./importFromFileReducers";
import tasks from "./taskManagementReducers";
import nationalities from "./nationalitiesReducers";
import timeline from "./timelineReducers";
import calendar from "./googleCalendarReducers";

export default combineReducers({
  settings,
  candidateActions,
  candidates,
  companies,
  jobs,
  users: UserReducer,
  languages,
  tags,
  skills,
  roles,
  permissions,
  timezone,
  jobTypes,
  status,
  educationDegree,
  emailTemplates,
  sendEmail,
  importFromFile,
  tasks,
  nationalities,
  timeline,
  calendar,
});
