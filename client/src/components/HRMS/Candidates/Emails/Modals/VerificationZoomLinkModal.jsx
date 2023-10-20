import React, { useState } from "react";
import { useSelector } from "react-redux";
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from "reactstrap";
import Toast from "../../../../constants/toast";
import { toast } from "react-toastify";
import axios from "axios";
import { baseURL } from "../../../../Shared/baseURL";
import { Loader } from "../../../../constants/loaders";
import { useTranslation } from "react-i18next";

const VerificationZoomLinkModal = ({
  verificationZoomLinkModalIsOpen,
  toggleVerificationZoomLinkModal,
}) => {
  const [isLoading, setIsLoading] = useState(false);

  const { t } = useTranslation();

  const authUser = useSelector((state) => state.users.auth_user.data?.user);

  const handleZoomLink = async () => {
    let token = localStorage.getItem("token");
    setIsLoading(true);
    return axios
      .get(`${baseURL}api/users/link-zoom-account/${authUser.id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then((response) => {
        setIsLoading(false);
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
        setIsLoading(false);
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

  return (
    <Modal
      isOpen={verificationZoomLinkModalIsOpen}
      toggle={toggleVerificationZoomLinkModal}
    >
      <ModalHeader toggle={toggleVerificationZoomLinkModal}>
        {t("verificationZoomLinkModal.headerHeading")}
      </ModalHeader>
      <ModalBody className="w-100 text-center">
        <h6 className="mb-4">{t("verificationZoomLinkModal.subHeading")}</h6>
        <Button
          type="button"
          color="primary"
          className="p-3"
          onClick={handleZoomLink}
        >
          {isLoading ? (
            <Loader />
          ) : (
            <>{t("verificationZoomLinkModal.verifyButton")}</>
          )}
        </Button>
      </ModalBody>
      <ModalFooter>
        <Button
          type="button"
          color="danger"
          onClick={toggleVerificationZoomLinkModal}
        >
          {t("buttons.close")}
        </Button>
      </ModalFooter>
      <Toast />
    </Modal>
  );
};

export default VerificationZoomLinkModal;
