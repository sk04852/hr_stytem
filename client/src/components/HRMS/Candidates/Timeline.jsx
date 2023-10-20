import React, { useEffect, useState } from "react";
import { Card, CardBody, CardHeader, CardTitle, Collapse } from "reactstrap";
import "./candidate.css";
import { useDispatch, useSelector } from "react-redux";
import {
  getCandidateTimeline,
  getCandidateProfileById,
} from "../../../redux/actions/candidatesAction";
import { DataLoader, Loader } from "../../constants/loaders";
import {
  Button,
  FormGroup,
  Label,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
} from "reactstrap";
import { NoRecordFound } from "../../constants/noRecordFound";
import { Field, Form, Formik } from "formik";
import { postTimelineComment } from "../../../redux/actions/timelineActions";
import { useParams } from "react-router-dom";
import { baseURL } from "../../Shared/baseURL";
import axios from "axios";
import { useTranslation } from "react-i18next";

const Timeline = ({ candidateItemById, triggerUpdate, setTriggerUpdate }) => {
  const { id } = useParams();
  const candidatesDataPerRow = 3;
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const timelineCommentsInitialValues = {
    timeline_id: "",
    body: "",
  };

  const timelineActionsInitialState = {
    candidatecv_id: "",
    timeline_action_type_id: "",
    timeline_action_type_comment: "",
  };

  const [timelineCommentsFormValues, setTimelineCommentsFormValues] = useState(
    timelineCommentsInitialValues
  );
  const [timelineActionsFormValues, setTimelineActionsFormValues] = useState(
    timelineActionsInitialState
  );
  const [timelineNewActionModal, setTimelineNewActionModal] = useState(false);
  const [nextCandidatesRow, setNextCandidatesRow] =
    useState(candidatesDataPerRow);
  const [timelineData, setTimelineData] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  const [loading, setLoading] = useState(false);
  const [candidateById, setCandidateById] = useState({});
  const [timelineID, setTimelineID] = useState(null);

  const [open, setOpen] = React.useState({});

  const handleCandidateDataPerRow = () => {
    setNextCandidatesRow(nextCandidatesRow + candidatesDataPerRow);
  };

  const timelineToggle = () => {
    setTimelineNewActionModal(!timelineNewActionModal);
  };

  const candidateSelector = useSelector((state) => state.candidates);
  const timelineSelector = useSelector((state) => state.timeline);
  const settingsSelector = useSelector((state) => state.settings);

  const fetchData = async () => {
    try {
      const response = await dispatch(getCandidateTimeline(id));
      setTimelineData(response.payload.timeline);
    } catch (error) {
      throw error;
    }
  };

  const fetchCandidateProfile = async () => {
    try {
      const response = await dispatch(getCandidateProfileById(id));
      setCandidateById(response.payload.cadidatecv);

      let existedData = {
        candidateById: id,
        timeline_action_type_id:
          response.payload.cadidatecv.timeline_action_type_id,
      };
      setTimelineActionsFormValues(existedData);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchData();
    fetchCandidateProfile();
  }, [triggerUpdate]);

  function handleClick(id, commentData) {
    const comment = commentData.comments;
    const commentID = commentData.id;

    setOpen((prevState) => ({ ...prevState, [id]: !prevState[id] }));

    if (comment.length > 0) {
      setTimelineID(null);
    } else {
      setTimelineID(commentID);
    }
  }

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      const sorted = [...timelineData].sort((a, b) =>
        a[e].toLowerCase() > b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("dsc");
    }
    if (sortingOrder === "dsc") {
      const sorted = [...timelineData].sort((a, b) =>
        a[e].toLowerCase() < b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("asc");
    }
  };

  const handleTimelineCommentSubmit = (values) => {
    // let element = document.getElementsByName("timeline_id");
    // values["timeline_id"] = element[0].value;
    values["timeline_id"] = timelineID;
    dispatch(postTimelineComment(values, id, fetchData));
  };

  const handleTimelineActionSubmit = (e) => {
    const token = localStorage.getItem("token");
    setLoading(true);
    return axios
      .post(
        `${baseURL}api/open/candidate_cv/timeline_action_type`,
        {
          candidatecv_id: id,
          timeline_action_type_id: e.timeline_action_type_id,
          timeline_action_type_comment: e.timeline_action_type_comment,
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      )
      .then((response) => {
        setLoading(false);
        setTimelineNewActionModal(false);
        fetchData();
      })
      .catch((error) => {
        setLoading(false);
        throw error;
      });
  };

  const renderTimelineNewActionModal = () => {
    return (
      <Modal isOpen={timelineNewActionModal} toggle={timelineToggle}>
        <ModalHeader
          toggle={timelineToggle}
          style={{
            fontSize: `${
              settingsSelector.FontSize === "Large"
                ? "large"
                : settingsSelector.FontSize === "Extra Large"
                ? "x-large"
                : "15px"
            }`,
          }}
        >
          {t("timelineHistory.newActivityModal.heading")}
        </ModalHeader>
        <Formik
          initialValues={timelineActionsFormValues}
          enableReinitialize={true}
          onSubmit={(e) => handleTimelineActionSubmit(e)}
        >
          <Form>
            <ModalBody>
              <FormGroup>
                <Label
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "0.875rem"
                    }`,
                  }}
                >
                  {t("timelineHistory.newActivityModal.call")}
                </Label>
                <div className="custom-controls-stacked d-flex">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="1"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.incoming")}
                    </span>
                  </Label>
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="2"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.outgoing")}
                    </span>
                  </Label>
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="3"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.answered")}
                    </span>
                  </Label>
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="4"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.notAnswered")}
                    </span>
                  </Label>
                </div>
              </FormGroup>
              <FormGroup>
                <Label
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "0.875rem"
                    }`,
                  }}
                >
                  {t("timelineHistory.newActivityModal.sms")}
                </Label>
                <div className="custom-controls-stacked">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="5"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.sms")}
                    </span>
                  </Label>
                </div>
              </FormGroup>
              <FormGroup>
                <Label
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "0.875rem"
                    }`,
                  }}
                >
                  {t("timelineHistory.newActivityModal.email")}
                </Label>
                <div className="custom-controls-stacked">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="6"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.email")}
                    </span>
                  </Label>
                </div>
              </FormGroup>
              <FormGroup>
                <Label
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "0.875rem"
                    }`,
                  }}
                >
                  {t("timelineHistory.newActivityModal.interview")}
                </Label>
                <div className="custom-controls-stacked d-flex">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="7"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.consent")}
                    </span>
                  </Label>
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="8"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.completed")}
                    </span>
                  </Label>
                </div>
              </FormGroup>
              <FormGroup>
                <Label
                  style={{
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "0.875rem"
                    }`,
                  }}
                >
                  {t("timelineHistory.newActivityModal.2ndInterview")}
                </Label>
                <div className="custom-controls-stacked">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="9"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.2ndInterview")}
                    </span>
                  </Label>
                </div>
              </FormGroup>
              <FormGroup>
                <div className="custom-controls-stacked">
                  <Label className="custom-control custom-radio custom-control-inline">
                    <Field
                      type="radio"
                      className="custom-control-input"
                      name="timeline_action_type_id"
                      value="10"
                    />
                    <span
                      className="custom-control-label"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "0.875rem"
                        }`,
                      }}
                    >
                      {t("timelineHistory.newActivityModal.comment")}
                    </span>
                  </Label>
                  <div className={"w-100"}>
                    <Field
                      as={"textarea"}
                      name={"timeline_action_type_comment"}
                      className={"w-100"}
                    />
                  </div>
                </div>
              </FormGroup>
            </ModalBody>
            <ModalFooter>
              <Button
                type={"submit"}
                color="primary"
                style={{
                  fontSize: `${
                    settingsSelector.FontSize === "Large"
                      ? "large"
                      : settingsSelector.FontSize === "Extra Large"
                      ? "x-large"
                      : "14px"
                  }`,
                }}
              >
                {loading ? <Loader /> : <>{t("buttons.save")}</>}
              </Button>{" "}
              <Button
                color="secondary"
                onClick={timelineToggle}
                style={{
                  fontSize: `${
                    settingsSelector.FontSize === "Large"
                      ? "large"
                      : settingsSelector.FontSize === "Extra Large"
                      ? "x-large"
                      : "14px"
                  }`,
                }}
              >
                {t("buttons.close")}
              </Button>
            </ModalFooter>
          </Form>
        </Formik>
      </Modal>
    );
  };

  return (
    <>
      {renderTimelineNewActionModal()}
      <Card>
        <CardHeader className="float-right w-100 d-flex justify-content-between">
          <CardTitle>
            <h5
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "1.75rem"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "2.25rem"
                    : "1.25rem"
                }`,
              }}
            >
              {t("timelineHistory.heading")}
            </h5>
          </CardTitle>
          <div>
            <Button
              type={"button"}
              color={"primary"}
              onClick={() => setTimelineNewActionModal(true)}
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "large"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "x-large"
                    : "14px"
                }`,
              }}
            >
              {t("timelineHistory.newActivityButton")}
            </Button>
          </div>
        </CardHeader>
        <CardBody>
          <table className={"table  mb-2 table-sortable"}>
            <thead>
              <tr>
                <th
                  onClick={() => handleSorting("title")}
                  className={sortingOrder === "asc" ? "asc" : "dsc"}
                  style={{
                    paddingRight: "50px",
                    cursor: "pointer",
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "14px"
                    }`,
                  }}
                >
                  {t("timelineHistory.name")}
                </th>

                <th
                  onClick={() => handleSorting("created_at")}
                  className={sortingOrder === "asc" ? "asc" : "dsc"}
                  style={{
                    paddingRight: "50px",
                    cursor: "pointer",
                    fontSize: `${
                      settingsSelector.FontSize === "Large"
                        ? "large"
                        : settingsSelector.FontSize === "Extra Large"
                        ? "x-large"
                        : "14px"
                    }`,
                  }}
                >
                  {t("timelineHistory.date")}
                </th>
              </tr>
            </thead>
          </table>
          {!candidateSelector.isLoading ? (
            <>
              {timelineData && timelineData.length > 0 ? (
                timelineData.slice(0, nextCandidatesRow).map((c, index) => (
                  <div key={index} className="timeline_item ">
                    <span>
                      <a>{c && c.body}</a>
                      <small
                        className="float-right text-right"
                        style={{
                          fontSize: `${
                            settingsSelector.FontSize === "Large"
                              ? "large"
                              : settingsSelector.FontSize === "Extra Large"
                              ? "x-large"
                              : "80%"
                          }`,
                        }}
                      >
                        {c &&
                          c.created_at.replace("T", " ").substring(11, 16) +
                            " " +
                            c.created_at.replace("T", " ").substring(8, 10) +
                            "-" +
                            c.created_at.replace("T", " ").substring(5, 7) +
                            "-" +
                            c.created_at.replace("T", " ").substring(0, 4)}
                      </small>
                    </span>
                    <h6
                      className="font600"
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      {c.userPr === null ? "" : c.userPr.user.name}
                    </h6>
                    <h6
                      style={{
                        fontSize: `${
                          settingsSelector.FontSize === "Large"
                            ? "large"
                            : settingsSelector.FontSize === "Extra Large"
                            ? "x-large"
                            : "1rem"
                        }`,
                      }}
                    >
                      {c && c.title}
                    </h6>
                    {c.detail_description !== null && (
                      <div
                        dangerouslySetInnerHTML={{
                          __html: c.detail_description,
                        }}
                      />
                    )}
                    {c.additional_information !== null && (
                      <div
                        dangerouslySetInnerHTML={{
                          __html: c.additional_information,
                        }}
                      />
                    )}
                    <div className="msg">
                      <a
                        onClick={() => handleClick(index, c)}
                        className="clickable text-muted"
                        role="button"
                      >
                        <i className="clickable fa fa-comments"></i>
                        {c.comments.length > 0 ? (
                          <>
                            {t(
                              "timelineHistory.timelineCommentDescription.view"
                            )}
                          </>
                        ) : (
                          <>
                            {t(
                              "timelineHistory.timelineCommentDescription.add"
                            )}
                          </>
                        )}
                      </a>
                      <Collapse isOpen={open[index]}>
                        <div>
                          <Formik
                            initialValues={timelineCommentsFormValues}
                            onSubmit={(values) =>
                              handleTimelineCommentSubmit(values)
                            }
                          >
                            <div className="collapse-event">
                              {c.comments.map((item, index) => (
                                <p key={index}>{item.body}</p>
                              ))}
                              <small
                                className="float-right font-14"
                                style={{
                                  fontSize: `${
                                    settingsSelector.FontSize === "Large"
                                      ? "large"
                                      : settingsSelector.FontSize ===
                                        "Extra Large"
                                      ? "x-large"
                                      : "14px"
                                  }`,
                                }}
                              >
                                {c.comments.length > 0 ? (
                                  <>
                                    {c.comments.map((item, index) => (
                                      <div key={index}>
                                        {item.created_at
                                          .replace("T", " ")
                                          .substring(11, 16) +
                                          " " +
                                          item.created_at
                                            .replace("T", " ")
                                            .substring(8, 10) +
                                          "-" +
                                          item.created_at
                                            .replace("T", " ")
                                            .substring(5, 7) +
                                          "-" +
                                          item.created_at
                                            .replace("T", " ")
                                            .substring(0, 4)}
                                      </div>
                                    ))}
                                  </>
                                ) : (
                                  ""
                                )}
                              </small>
                              {c.comments.length > 0 ? null : (
                                <Form className="well">
                                  <FormGroup>
                                    <Field
                                      type={"hidden"}
                                      name={"timeline_id"}
                                      value={c.id}
                                    />
                                    <Field
                                      as={"textarea"}
                                      rows="2"
                                      name="body"
                                      className="form-control no-resize"
                                      placeholder="Enter comment..."
                                      onClick={() => {
                                        if (c.comments.length === 0) {
                                          setTimelineID(c.id);
                                        }
                                      }}
                                      style={{
                                        fontSize: `${
                                          settingsSelector.FontSize === "Large"
                                            ? "large"
                                            : settingsSelector.FontSize ===
                                              "Extra Large"
                                            ? "x-large"
                                            : "14px"
                                        }`,
                                      }}
                                    />
                                  </FormGroup>
                                  <Button
                                    className="btn btn-primary"
                                    type="submit"
                                    disabled={timelineSelector.isLoading}
                                    style={{
                                      fontSize: `${
                                        settingsSelector.FontSize === "Large"
                                          ? "large"
                                          : settingsSelector.FontSize ===
                                            "Extra Large"
                                          ? "x-large"
                                          : "14px"
                                      }`,
                                    }}
                                  >
                                    {timelineSelector.isLoading ? (
                                      <Loader />
                                    ) : (
                                      <>{t("buttons.save")}</>
                                    )}
                                  </Button>
                                </Form>
                              )}
                            </div>
                          </Formik>
                        </div>
                      </Collapse>
                    </div>
                  </div>
                ))
              ) : (
                <NoRecordFound />
              )}
            </>
          ) : (
            <DataLoader />
          )}
          <div className={"w-100 text-center"}>
            {timelineData && nextCandidatesRow < timelineData.length && (
              <Button
                className="mt-4"
                color={"primary"}
                onClick={handleCandidateDataPerRow}
                style={{
                  fontSize: `${
                    settingsSelector.FontSize === "Large"
                      ? "large"
                      : settingsSelector.FontSize === "Extra Large"
                      ? "x-large"
                      : "14px"
                  }`,
                }}
              >
                {t("timelineHistory.loadMore")}
              </Button>
            )}
          </div>
        </CardBody>
      </Card>
    </>
  );
};

export default Timeline;
