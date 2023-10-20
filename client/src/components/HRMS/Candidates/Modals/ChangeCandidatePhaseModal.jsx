import React, { useState, useEffect } from "react";
import {
  Button,
  Card,
  ListGroup,
  ListGroupItem,
  Modal,
  ModalBody,
  ModalHeader,
} from "reactstrap";
import { useSelector, useDispatch } from "react-redux";
import { useHistory } from "react-router-dom";
import { DataLoader } from "../../../constants/loaders";
import {
  candidatesApplyToJob,
  getCandidatePhaseJobs,
} from "../../../../redux/actions/candidatesAction";
import { FormValidationErrors } from "../../../constants/errors";
import { useTranslation } from "react-i18next";
import { NoJobFound } from "../../../constants/noRecordFound";

const ChangeCandidatePhaseJobsModal = ({
  toggleChangeCandidatePhaseModal,
  changePhaseModalIsOpen,
  phaseActionId,
  actionTemplateKey,
  candidateId,
  triggerUpdate,
  setTriggerUpdate,
}) => {
  const dispatch = useDispatch();
  const history = useHistory();

  const { t } = useTranslation();

  const [candidateAppliedJobs, setCandidateAppliedJobs] = useState([]);
  const [errors, setErrors] = useState({});

  const settingsSelector = useSelector((state) => state.settings);
  const candidateAppliedJobsSelector = useSelector((state) => state.candidates);

  const fetchCandidateAppliedJobs = async () => {
    if (changePhaseModalIsOpen === true) {
      try {
        const response = await dispatch(
          getCandidatePhaseJobs(candidateId, phaseActionId)
        );
        setCandidateAppliedJobs(response.payload.data);
      } catch (error) {
        throw error;
      }
    }
  };

  useEffect(() => {
    fetchCandidateAppliedJobs();
  }, [triggerUpdate, changePhaseModalIsOpen]);

  const candidateApplyToJob = (action_id, candidatecv_id, job_id) => {
    if (action_id === 7) {
      const confirm = window.confirm(
        "Are you sure? It will reject you from another jobs."
      );
      if (confirm === true) {
        dispatch(
          candidatesApplyToJob(
            action_id,
            candidatecv_id,
            job_id,
            actionTemplateKey,
            history,
            setTriggerUpdate,
            setErrors,
            ""
          )
        );
      }
    } else {
      dispatch(
        candidatesApplyToJob(
          action_id,
          candidatecv_id,
          job_id,
          actionTemplateKey,
          history,
          setTriggerUpdate,
          setErrors,
          ""
        )
      );
    }
  };

  return (
    <Modal
      isOpen={changePhaseModalIsOpen}
      toggle={toggleChangeCandidatePhaseModal}
    >
      <ModalHeader toggle={toggleChangeCandidatePhaseModal}>
        Applied Jobs
      </ModalHeader>

      {/*ERRORS*/}
      {errors && Object.keys(errors).length !== 0 ? (
        <FormValidationErrors errors={errors} />
      ) : null}

      <ModalBody>
        <Card>
          {!candidateAppliedJobsSelector.isLoading ? (
            candidateAppliedJobs && candidateAppliedJobs.length > 0 ? (
              candidateAppliedJobs.map((job, index) => (
                <ListGroup key={index}>
                  <ListGroupItem className="p-0 mb-1">
                    <Button
                      type={"button"}
                      className={
                        "w-100 bg-none border-0 text-dark hover-overlay apply-to-job-button"
                      }
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "14px"
                        }`,
                        cursor: "pointer",
                      }}
                      onClick={() => {
                        candidateApplyToJob(
                          phaseActionId,
                          job.candidatecv_id,
                          job.job_pr_id
                        );
                      }}
                    >
                      <div className={"text-left"}>
                        <strong>
                          {t("candidates.candidateApplyForJobModal.jobOffer")}:{" "}
                        </strong>
                        {job.offer_name} <br />
                        <strong>
                          {t(
                            "candidates.candidateApplyForJobModal.jobLocation"
                          )}
                          :{" "}
                        </strong>
                        {job.location} <br />
                        <strong>
                          {t("candidates.candidateApplyForJobModal.jobType")}:{" "}
                        </strong>
                        {job.job_type}
                      </div>
                    </Button>
                  </ListGroupItem>
                </ListGroup>
              ))
            ) : (
              <NoJobFound />
            )
          ) : (
            <DataLoader />
          )}
        </Card>
      </ModalBody>
    </Modal>
  );
};

export default ChangeCandidatePhaseJobsModal;
