import * as ActionTypes from "../actionTypes";
import axios from "axios";
import { baseURL } from "../../components/Shared/baseURL";
import { toast } from "react-toastify";

export const sendEmailSuccess = (data) => ({
  type: ActionTypes.SEND_EMAIL_SUCCESS,
  payload: data,
});

export const sendEmailProcessing = () => ({
  type: ActionTypes.SEND_EMAIL_PROCESSING,
});

export const sendEmailFailed = (error) => ({
  type: ActionTypes.SEND_EMAIL_FAILED,
  payload: error,
});

export const sendEmail = (formData, id, history, location) => (dispatch) => {
  dispatch(sendEmailProcessing());

  const token = localStorage.getItem("token");
  return axios
    .post(`${baseURL}api/mail/send-mail`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data",
      },
    })
    .then((response) => {
      dispatch(sendEmailSuccess(response.data));
      toast.success(response.data.message, {
        position: "top-right",
        autoClose: 3000,
        hideProgressBar: true,
        closeOnClick: true,
        pauseOnHover: true,
        draggable: true,
        progress: undefined,
      });
      setTimeout(() => {
        if (location.pathname === `/hr-candidate/view/${id}/send-email`) {
          history.push(`/hr-candidate/view/${id}/1`);
        } else if (
          location.pathname === `/hr-candidate/view/${id}/send-link-email`
        ) {
          history.push(`/hr-candidate/view/${id}/1`);
        } else if (
          location.pathname === `/hr-companies/view/${id}/send-email`
        ) {
          history.push(`/hr-companies/view/${id}`);
        } else if (
          location.pathname === `/hr-companies/view/${id}/send-email-link`
        ) {
          history.push(`/hr-companies/view/${id}`);
        }
      }, 3000);
    })
    .catch((error) => {
      dispatch(sendEmailFailed(error.response.data));
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

export const sendCompanyEmailLinkSuccess = (data) => ({
  type: ActionTypes.SEND_COMPANY_EMAIL_LINK_SUCCESS,
  payload: data,
});

export const sendCompanyEmailLinkProcessing = () => ({
  type: ActionTypes.SEND_COMPANY_EMAIL_LINK_PROCESSING,
});

export const sendCompanyEmailLinkFailed = (error) => ({
  type: ActionTypes.SEND_COMPANY_EMAIL_LINK_FAILED,
  payload: error,
});

export const sendCompanyEmailLink =
  (formData, id, history, location, companyPrId, setErrors) => (dispatch) => {
    dispatch(sendCompanyEmailLinkProcessing());

    const token = localStorage.getItem("token");
    return axios
      .post(`${baseURL}api/mail/send-company-mail`, formData, {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "multipart/form-data",
        },
      })
      .then((response) => {
        dispatch(sendCompanyEmailLinkSuccess(response.data));
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
        setTimeout(() => {
          if (
            location.pathname === `/hr-companies/view/${id}/send-email-link`
          ) {
            history.push(`/hr-companies/view/${id}`);
          } else if (
            location.pathname ===
            `/hr-jobs/view/${id}/send-verification-link/${companyPrId}`
          ) {
            history.push(`/hr-jobs/view/${id}`);
          }
        }, 3000);
      })
      .catch((error) => {
        dispatch(sendCompanyEmailLinkFailed(error.response.data));
        if (error.response.data.errors) {
          setErrors(error.response.data.errors);
        }
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
