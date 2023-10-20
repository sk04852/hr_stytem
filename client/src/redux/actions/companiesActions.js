import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";

export const fetchCompanyDataSuccess = (data) => ({
  type: ActionTypes.FETCH_COMPANY_DATA_SUCCESS,
  payload: data,
});

export const fetchCompanyDataProcessing = () => ({
  type: ActionTypes.FETCH_COMPANY_DATA_PROCESSING,
});

export const fetchCompanyDataFailed = (error) => ({
  type: ActionTypes.FETCH_COMPANY_DATA_FAILED,
  payload: error,
});

export const fetchCompanyData = (company_name, setFormValues) => (dispatch) => {
  dispatch(fetchCompanyDataProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/companies/fetch-company-detail-data`,
      { company_name: company_name },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(fetchCompanyDataSuccess(response.data));

      let initialCompanyData = {
        invoicing_info: "",
        industry_names: [],
        vat: "",
        en: {
          company_name: "",
          contacts: [],
          location: [],
        },
      };

      if (response.data.data.keha.ettevotjad.item.length > 0) {
        response.data.data.keha.ettevotjad.item.forEach((items, index) => {
          if (items.nimi == company_name) {
            initialCompanyData.invoicing_info = items.ariregistri_kood;
            initialCompanyData.en.company_name = items.nimi;
            initialCompanyData.vat = items.isikuandmed.kmkr_number
              ? items.isikuandmed.kmkr_number
              : "";
            initialCompanyData.vat =
              Object.keys(items.kmkr_number).length === 0
                ? ""
                : items.kmkr_number;

            // company contacts
            let cardlessPersons = items.isikuandmed.kaardile_kantud_isikud;
            let communicationMeans = items.yldandmed.sidevahendid;
            for (let cardlessPerson of cardlessPersons.item) {
              let temp_object = {
                name:
                  Object.getOwnPropertyNames(cardlessPerson.eesnimi).length ===
                  0
                    ? ""
                    : cardlessPerson.eesnimi +
                      " " +
                      cardlessPerson.nimi_arinimi,
                position: cardlessPerson.isiku_roll_tekstina,
                email: "",
                phone: "",
              };
              for (let communicationMean of communicationMeans.item) {
                if (
                  communicationMean.liik_tekstina === "Elektronposti aadress" &&
                  temp_object.email == ""
                ) {
                  temp_object.email = communicationMean.sisu;
                } else if (
                  communicationMean.liik_tekstina === "Elektronposti aadress" &&
                  temp_object.email.length > 0
                ) {
                  temp_object.email += "," + communicationMean.sisu;
                }

                if (
                  communicationMean.liik_tekstina === "Telefon" &&
                  temp_object.phone == ""
                ) {
                  temp_object.phone = communicationMean.sisu;
                } else if (
                  communicationMean.liik_tekstina === "Telefon" &&
                  temp_object.phone.length > 0
                ) {
                  temp_object.phone += "," + communicationMean.sisu;
                }
              }
              initialCompanyData.en.contacts.push(temp_object);
            }

            // company industry
            let reportedActivities = items.yldandmed.teatatud_tegevusalad;
            let reportedActivitiesArr = [];
            if (reportedActivities.item.length > 0) {
              for (let industryItems of reportedActivities.item) {
                reportedActivitiesArr.push(
                  industryItems.nace_kood + " " + industryItems.emtak_tekstina
                );
              }
            }
            let uniqueReportedActivities = [...new Set(reportedActivitiesArr)];
            if (uniqueReportedActivities.length > 0) {
              initialCompanyData.industry_names = uniqueReportedActivities;
            }

            // company location
            if (items.yldandmed.aadressid.item.length > 0) {
              for (let companyAddress of items.yldandmed.aadressid.item) {
                if (
                  Object.getOwnPropertyNames(
                    companyAddress.aadress_ads__ads_normaliseeritud_taisaadress
                  ).length !== 0
                ) {
                  initialCompanyData.en.location.push(
                    companyAddress.aadress_ads__ads_normaliseeritud_taisaadress +
                      ", " +
                      companyAddress.postiindeks
                  );
                }
              }
            }
            setFormValues(initialCompanyData);
          }
        });
      }

      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(fetchCompanyDataFailed(error.response.data));
        toast.error("No Company Found", {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        if (error.response.status === 500) {
          toast.error("Internal Server Error", {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: true,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
          });
        }
      }
    });
};

export const getCompaniesSuccess = (data) => ({
  type: ActionTypes.GET_COMPANIES_SUCCESS,
  payload: data,
});

export const getCompaniesProcessing = () => ({
  type: ActionTypes.GET_COMPANIES_PROCESSING,
});

export const getCompaniesFailed = (error) => ({
  type: ActionTypes.GET_COMPANIES_FAILED,
  payload: error,
});

export const getCompaniesJobsSuccess = (data) => ({
  type: ActionTypes.GET_COMPANIES_JOBS_SUCCESS,
  payload: data,
});

export const getCompaniesJobsProcessing = () => ({
  type: ActionTypes.GET_COMPANIES_JOBS_PROCESSING,
});

export const getCompaniesJobsFailed = (error) => ({
  type: ActionTypes.GET_COMPANIES_JOBS_FAILED,
  payload: error,
});

export const getAllCompanies =
  (
    pageNumber,
    searchTerm,
    companyName,
    companyIndustry,
    companyLocation,
    sorting
  ) =>
  (dispatch) => {
    dispatch(getCompaniesProcessing());
    const token = localStorage.getItem("token");
    return axios
      .get(
        `${baseURL}api/companies?page=${pageNumber}&filter[search]=${searchTerm}&filter[name]=${
          companyName ? companyName : ""
        }&filter[industry_id]=${
          companyIndustry ? companyIndustry : ""
        }&filter[location]=${companyLocation ? companyLocation : ""}&sort=${
          sorting !== null ? sorting : ""
        }`,
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => dispatch(getCompaniesSuccess(response.data)))
      .catch((error) => {
        if (error.response !== undefined) {
          dispatch(getCompaniesFailed(error.response.data));
        }
      });
  };

export const getCompaniesCandidates = (id) => (dispatch) => {
  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/companies/` + id + `/candidates`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompaniesJobsSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCompaniesJobsFailed(error.response.data));
      }
    });
};

export const getCompaniesIndustrySuccess = (data) => ({
  type: ActionTypes.GET_COMPANIES_INDUSTRY_SUCCESS,
  payload: data,
});

export const getCompaniesIndustryProcessing = () => ({
  type: ActionTypes.GET_COMPANIES_INDUSTRY_PROCESSING,
});

export const getCompaniesIndustryFailed = (error) => ({
  type: ActionTypes.GET_COMPANIES_INDUSTRY_FAILED,
  payload: error,
});

export const getAllCompaniesIndustry = () => (dispatch) => {
  dispatch(getCompaniesIndustryProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/companies/industries`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompaniesIndustrySuccess(response.data)))
    .catch((error) => dispatch(getCompaniesIndustryFailed(error)));
};

export const getCompanyProfileByIdSuccess = (data) => ({
  type: ActionTypes.GET_COMPANY_BY_ID_SUCCESS,
  payload: data,
});

export const getCompanyProfileByIdProcessing = () => ({
  type: ActionTypes.GET_COMPANY_BY_ID_PROCESSING,
});

export const getCompanyProfileByIdFailed = (error) => ({
  type: ActionTypes.GET_COMPANY_BY_ID_FAILED,
  payload: error,
});

export const getAllCompanyProfileById = (id) => (dispatch) => {
  dispatch(getCompanyProfileByIdProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/companies/` + id + "/complete-profile", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompanyProfileByIdSuccess(response.data)))
    .catch((error) => dispatch(getCompanyProfileByIdFailed(error)));
};

export const getCompanyRelevantJobsSuccess = (data) => ({
  type: ActionTypes.GET_COMPANY_RELEVANT_JOBS_SUCCESS,
  payload: data,
});

export const getCompanyDataSuccess = (data) => ({
  type: ActionTypes.GET_COMPANY_SUCCESS,
  payload: data,
});

export const getCompanyDataFailed = (data) => ({
  type: ActionTypes.GET_COMPANY_FAILED,
  payload: data,
});

export const getCompanyRelevantJobsProcessing = () => ({
  type: ActionTypes.GET_COMPANY_RELEVANT_JOBS_PROCESSING,
});

export const getCompanyRelevantJobsFailed = (error) => ({
  type: ActionTypes.GET_COMPANY_RELEVANT_JOBS_FAILED,
  payload: error,
});

export const getAllCompanyRelevantJobs = (id) => (dispatch) => {
  dispatch(getCompanyRelevantJobsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/companies/` + id + "/jobs", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompanyRelevantJobsSuccess(response.data)))
    .catch((error) => dispatch(getCompanyRelevantJobsFailed(error)));
};

export const getCompanyEmailTemplateSuccess = (data) => ({
  type: ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_SUCCESS,
  payload: data,
});

export const getCompanyEmailTemplateProcessing = () => ({
  type: ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_PROCESSING,
});

export const getCompanyEmailTemplateFailed = (error) => ({
  type: ActionTypes.GET_COMPANY_EMAIL_TEMPLATE_DATA_FAILED,
  payload: error,
});

export const getTemplateData = (id, templateID) => (dispatch) => {
  dispatch(getCompanyEmailTemplateProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates/${templateID}/company/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompanyEmailTemplateSuccess(response)))
    .catch((error) => {
      dispatch(getCompanyEmailTemplateFailed(error.response.data));
      if (error.response !== undefined) {
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const getCompanyEmailLinkSuccess = (data) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_LINK_SUCCESS,
  payload: data,
});

export const getCompanyEmailLinkProcessing = () => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_LINK_PROCESSING,
});

export const getCompanyEmailLinkFailed = (error) => ({
  type: ActionTypes.GET_EMAIL_TEMPLATES_LINK_FAILED,
  payload: error,
});

export const getCompanyEmailLinkData = (id, templateID) => (dispatch) => {
  dispatch(getCompanyEmailLinkProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/mail/templates/${templateID}/company-job/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompanyEmailLinkSuccess(response.data)))
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(getCompanyEmailLinkFailed(error.response.data));
      }
    });
};

export const getCompanyTimelineSuccess = (data) => ({
  type: ActionTypes.GET_COMPANY_TIMELINE_SUCCESS,
  payload: data,
});

export const getCompanyTimelineProcessing = () => ({
  type: ActionTypes.GET_COMPANY_TIMELINE_PROCESSING,
});

export const getCompanyTimelineFailed = (error) => ({
  type: ActionTypes.GET_COMPANY_TIMELINE_FAILED,
  payload: error,
});

export const getCompanyTimeline = (id) => (dispatch) => {
  dispatch(getCompanyTimelineProcessing());

  const token = localStorage.getItem("token");
  return axios
    .get(`${baseURL}api/companies/timeline/` + id, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then((response) => dispatch(getCompanyTimelineSuccess(response.data)))
    .catch((error) => dispatch(getCompanyTimelineFailed(error)));
};

// export const fetchCompanyData = () => (dispatch) => {
//   dispatch(getCompanyRelevantJobsProcessing());
//
//   const token = localStorage.getItem("token");
//   return axios
//     .post(`${baseURL}api/companies/fetch-company-data`, {
//       headers: {
//         Authorization: `Bearer ${token}`,
//       },
//     })
//     .then((response) => dispatch(getCompanyDataSuccess(response.data)))
//     .catch((error) => dispatch(getCompaniesFailed(error)));
// };

export const createCompaniesSuccess = (data) => ({
  type: ActionTypes.POST_COMPANIES_SUCCESS,
  payload: data,
});

export const createCompaniesProcessing = () => ({
  type: ActionTypes.POST_COMPANIES_PROCESSING,
});

export const createCompaniesFailed = (error) => ({
  type: ActionTypes.POST_COMPANIES_FAILED,
  payload: error,
});

export const createCompanies =
  (formData, setUploadFiles, setErrors, history) => (dispatch) => {
    dispatch(createCompaniesProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/companies`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(createCompaniesSuccess(response.data));
        document.getElementById("add-companies-form").reset();
        setUploadFiles([]);
        setErrors({});
        history.push("/hr-companies");
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      })
      .catch((error) => {
        dispatch(createCompaniesFailed(error.response.data));
        if (error.response.data.errors) {
          setErrors(error.response.data.errors);
        }
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };

export const postCompanyTimelineCommentsSuccess = (data) => ({
  type: ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_SUCCESS,
  payload: data,
});

export const postCompanyTimelineCommentsProcessing = () => ({
  type: ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_PROCESSING,
});

export const postCompanyTimelineCommentsFailed = (error) => ({
  type: ActionTypes.POST_COMPANY_TIMELINE_COMMENTS_FAILED,
  payload: error,
});

export const postCompanyTimelineComments =
  (company_pr_id, candidatecv_id, user_pr_id, inputFields) => (dispatch) => {
    dispatch(postCompanyTimelineCommentsProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(
        ``,
        {
          company_pr_id: company_pr_id,
          candidatecv_id: candidatecv_id,
          user_pr_id: user_pr_id,
          comments: inputFields,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      )
      .then((response) => {
        dispatch(postCompanyTimelineCommentsSuccess(response.data));
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      })
      .catch((error) => {
        dispatch(postCompanyTimelineCommentsFailed(error));
        toast.error(error.response.data.error, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };

export const updateCompanySuccess = (data) => ({
  type: ActionTypes.UPDATE_COMPANY_SUCCESS,
  payload: data,
});
export const updateCompanyProcessing = () => ({
  type: ActionTypes.UPDATE_COMPANY_PROCESSING,
});
export const updateCompanyFailed = (error) => ({
  type: ActionTypes.UPDATE_COMPANY_FAILED,
  payload: error,
});

export const updateCompany =
  (formData, id, setErrors, setUploadFiles, history) => (dispatch) => {
    dispatch(updateCompanyProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/companies/update`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(updateCompanySuccess(response.data));
        dispatch(getAllCompanyProfileById(id));
        setUploadFiles([]);
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
        history.push(`/hr-companies/view/${id}`);
      })
      .catch((error) => {
        dispatch(updateCompanyFailed(error.response.data));
        setErrors(error.response.data.errors);
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };

export const deleteCompaniesSuccess = (data) => ({
  type: ActionTypes.DELETE_COMPANIES_SUCCESS,
  payload: data,
});
export const deleteCompaniesProcessing = () => ({
  type: ActionTypes.DELETE_COMPANIES_PROCESSING,
});
export const deleteCompaniesFailed = (error) => ({
  type: ActionTypes.DELETE_COMPANIES_FAILED,
  payload: error,
});

export const deleteCompanies = (id, setData, fetchCompanies) => (dispatch) => {
  dispatch(deleteCompaniesProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/companies/delete`,
      {
        id: id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteCompaniesSuccess(response));
      setData(fetchCompanies("", "", ""));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      if (error.response !== undefined) {
        dispatch(deleteCompaniesFailed(error.response));
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      }
    });
};

export const deleteViewCompaniesUploadsSuccess = (data) => ({
  type: ActionTypes.DELETE_VIEW_COMPANIES_UPLOADS_SUCCESS,
  payload: data,
});
export const deleteViewCompaniesUploadsProcessing = () => ({
  type: ActionTypes.DELETE_VIEW_COMPANIES_UPLOADS_PROCESSING,
});
export const deleteViewCompaniesUploadsFailed = (error) => ({
  type: ActionTypes.DELETE_VIEW_COMPANIES_UPLOADS_FAILED,
  payload: error,
});

export const deleteViewCompaniesUploads = (id, profileId) => (dispatch) => {
  dispatch(deleteViewCompaniesUploadsProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(
      `${baseURL}api/companies/file/delete`,
      {
        id: id,
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    )
    .then((response) => {
      dispatch(deleteViewCompaniesUploadsSuccess(response));
      dispatch(getAllCompanyProfileById(profileId));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    })
    .catch((error) => {
      dispatch(deleteViewCompaniesUploadsFailed(error));
      toast.error("Something went wrong. Please try again.", {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
    });
};

export const addNewCompanyContactSuccess = (data) => ({
  type: ActionTypes.Add_NEW_COMPANY_CONTACT_SUCCESS,
  payload: data,
});
export const addNewCompanyContactProcessing = () => ({
  type: ActionTypes.Add_NEW_COMPANY_CONTACT_PROCESSING,
});
export const addNewCompanyContactFailed = (error) => ({
  type: ActionTypes.Add_NEW_COMPANY_CONTACT_FAILED,
  payload: error,
});

export const addNewCompanyContact =
  (id, values, setAddNewContactModal, fetchCompanyContacts, setErrors) =>
  (dispatch) => {
    dispatch(addNewCompanyContactProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/companies/${id}/contact`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(addNewCompanyContactSuccess(response.data));
        setAddNewContactModal(false);
        fetchCompanyContacts();
        setErrors({});
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      })
      .catch((error) => {
        dispatch(addNewCompanyContactFailed(error.response.data));
        setErrors(error.response.data.errors);
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };

export const addNewCompanyContactJobVerification =
  (id, values, setAddNewContactModal, fetchCompanyContacts, setErrors) =>
  (dispatch) => {
    dispatch(addNewCompanyContactProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/companies/${id}/contact`, values, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        dispatch(addNewCompanyContactSuccess(response.data));
        setAddNewContactModal(false);
        fetchCompanyContacts();
        setErrors({});
        toast.success(response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      })
      .catch((error) => {
        dispatch(addNewCompanyContactFailed(error.response.data));
        setErrors(error.response.data.errors);
        toast.error(error.response.data.message, {
          position: "top-right",
          autoClose: 3000,
          hideProgressBar: true,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined,
        });
      });
  };
