import React, { useState } from "react";
import {
  Button,
  Card,
  Col,
  ListGroup,
  ListGroupItem,
  Progress,
  Row,
} from "reactstrap";
import { useSelector, useDispatch } from "react-redux";
import { DataLoader } from "../../constants/loaders";
import { NoMatchFound } from "../../constants/noRecordFound";
import { candidatesApplyToJob } from "../../../redux/actions/candidatesAction";
import { useTranslation } from "react-i18next";
import { FormValidationErrors } from "../../constants/errors";
import { screenHeight } from "../../Shared/WindowDimensions";

const BestJobMatch = ({
  bestMatchData,
  candidateId,
  setTriggerUpdate,
  handleFetchBestMatchingJobs,
  fetchAllCandidates,
  fetchCandidateAppliedJobs,
  offerPage,
  setFetchAppliedJobsUpdate,
}) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [errors, setErrors] = useState({});

  const candidateSelector = useSelector((state) => state.candidates);
  const settingsSelector = useSelector((state) => state.settings);

  const candidateApplyToJob = (action_id, candidatecv_id, job_id) => {
    dispatch(
      candidatesApplyToJob(
        action_id,
        candidatecv_id,
        job_id,
        "",
        "",
        setTriggerUpdate,
        setErrors,
        handleFetchBestMatchingJobs,
        offerPage,
        setFetchAppliedJobsUpdate
      )
    );
  };

  const MatchingTags = ({ tags }) => {
    let tagsArr = [];
    for (const tag in tags.highlight) {
      if (Object.hasOwnProperty.call(tags.highlight, tag)) {
        const element = tags.highlight[tag];
        tagsArr.push(element);
      }
    }

    let unique = tagsArr.map((item) => {
      return [...new Set(item)];
    });

    return (
      <Row>
        <Col sm={12} md={12} className={"w-100 d-flex"}>
          {unique?.map((item, index) => (
            <div
              key={index}
              className={"mr-2"}
              dangerouslySetInnerHTML={{ __html: item }}
            />
          ))}
        </Col>
      </Row>
    );
  };

  return (
    <Row>
      {/*ERRORS*/}
      {errors && Object.keys(errors).length !== 0 ? (
        <FormValidationErrors errors={errors} />
      ) : null}
      <div
        className={"w-100"}
        style={{
          overflow: "auto",
          height: `${screenHeight - 200}px`,
        }}
      >
        {!candidateSelector.isLoading ? (
          <>
            {bestMatchData &&
            bestMatchData.length &&
            bestMatchData.length > 0 ? (
              // <table className="table mb-0" id="myTable">
              <Card>
                {bestMatchData.map((jobItem, index) => (
                  <ListGroup key={index}>
                    <ListGroupItem className="p-0 mb-1">
                      {/* <Row>
                        <Col sm={12} md={12}> */}
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
                            2,
                            candidateId,
                            jobItem.document.id
                          );
                        }}
                      >
                        <div className={"text-left"}>
                          <strong>
                            {t("candidates.candidateApplyForJobModal.jobOffer")}
                            :{" "}
                          </strong>
                          {jobItem.document.content.offer_name} <br />
                          <strong>
                            {t(
                              "candidates.candidateApplyForJobModal.jobLocation"
                            )}
                            :{" "}
                          </strong>
                          {jobItem.document.content.location} <br />
                          <strong>
                            {t("candidates.candidateApplyForJobModal.jobType")}:{" "}
                          </strong>
                          {jobItem.document.content.job_type} <br />
                          <span>
                            <strong>
                              {t(
                                "candidates.candidateApplyForJobModal.jobMatching"
                              )}
                              :{" "}
                            </strong>
                            {`${
                              jobItem.score > 100
                                ? "100"
                                : jobItem.score.toFixed(2)
                            }%`}
                          </span>
                          {jobItem.highlight !== null ? (
                            <MatchingTags tags={jobItem} />
                          ) : null}
                          <Progress
                            value={jobItem.score.toFixed(2)}
                            color={
                              jobItem.score.toFixed(2) >= 0 &&
                              jobItem.score.toFixed(2) <= 50
                                ? "danger"
                                : jobItem.score.toFixed(2) >= 51 &&
                                  jobItem.score.toFixed(2) <= 70
                                ? "warning"
                                : "success"
                            }
                            style={{ width: "200px", height: "0.5rem" }}
                            className={"mt-1"}
                          />
                        </div>
                      </Button>
                      {/* </Col>
                      </Row> */}
                    </ListGroupItem>
                  </ListGroup>
                ))}
              </Card>
            ) : (
              // </table>
              <NoMatchFound />
            )}
          </>
        ) : (
          <DataLoader />
        )}
      </div>
    </Row>
  );
};

export default BestJobMatch;
