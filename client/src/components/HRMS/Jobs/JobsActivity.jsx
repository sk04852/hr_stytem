import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { getJobsTimeline } from "../../../redux/actions/jobsAction";
import { DataLoader, Loader } from "../../constants/loaders";
import {
  Collapse,
  Button,
  Card,
  CardTitle,
  CardHeader,
  CardBody,
  FormGroup,
} from "reactstrap";
import { NoRecordFound } from "../../constants/noRecordFound";
import { postTimelineComment } from "../../../redux/actions/timelineActions";
import { Field, Form, Formik } from "formik";
import { useTranslation } from "react-i18next";

const JobsActivity = ({ job, id }) => {
  const jobsDataPerRow = 3;
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const timelineCommentsInitialValues = {
    timeline_id: "",
    body: "",
  };

  const [timelineCommentsFormValues, setTimelineCommentsFormValues] = useState(
    timelineCommentsInitialValues
  );
  const [timelinedata, setTimelineData] = useState("");
  const [sortingOrder, setSortingOrder] = useState("asc");
  const [nextjobsRow, setNextjobsRow] = useState(jobsDataPerRow);
  const [open, setOpen] = useState({});

  const handlejobDataPerRow = () => {
    setNextjobsRow(nextjobsRow + jobsDataPerRow);
  };

  function handleClick(id) {
    setOpen((prevState) => ({ ...prevState, [id]: !prevState[id] }));
  }

  const handleSorting = (e) => {
    if (sortingOrder === "asc") {
      const sorted = [...timelinedata].sort((a, b) =>
        a[e].toLowerCase() > b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("dsc");
    }
    if (sortingOrder === "dsc") {
      const sorted = [...timelinedata].sort((a, b) =>
        a[e].toLowerCase() < b[e].toLowerCase() ? 1 : -1
      );
      setTimelineData(sorted);
      setSortingOrder("asc");
    }
  };

  const jobsSelector = useSelector((state) => state.jobs);
  const timelineSelector = useSelector((state) => state.timeline);
  const settingsSelector = useSelector((state) => state.settings);

  const fetchData = async () => {
    try {
      const response = await dispatch(getJobsTimeline(id));
      setTimelineData(response.payload.timeline);
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleTimelineCommentSubmit = (values) => {
    let element = document.getElementsByName("timeline_id");
    values["timeline_id"] = element[0].value;
    dispatch(postTimelineComment(values, id, fetchData));
  };

  return (
    <>
      <div style={{ padding: "0px 9px 0px 10px" }}>
        <Card>
          {
            <>
              <CardHeader>
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
                    {t("jobs.timeline.heading")}{" "}
                    <b>
                      {" "}
                      {job.jobs === undefined
                        ? ""
                        : job.jobs[0].offer_name}{" "}
                    </b>
                  </h5>
                </CardTitle>
              </CardHeader>
              <CardBody>
                <table className={"table  mb-2 table-sortable"}>
                  <thead>
                    <tr>
                      <th
                        onClick={() => handleSorting("name")} //Backend issue
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
                        {t("jobs.timeline.name")}
                      </th>

                      <th
                        onClick={() => handleSorting("updated_at")}
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
                        {t("jobs.timeline.date")}
                      </th>
                    </tr>
                  </thead>
                </table>
                {!jobsSelector.isLoading ? (
                  <>
                    {timelinedata.length > 0 ? (
                      timelinedata.slice(0, nextjobsRow).map((c, index) => (
                        <div key={index} className="timeline_item ">
                          <span>
                            <a>{c && c.body}</a>
                            <small
                              className="float-right text-right"
                              style={{
                                fontSize: `${
                                  settingsSelector.FontSize === "Large"
                                    ? "large"
                                    : settingsSelector.FontSize ===
                                      "Extra Large"
                                    ? "x-large"
                                    : "80%"
                                }`,
                              }}
                            >
                              {c &&
                                c.created_at
                                  .replace("T", " ")
                                  .substring(11, 16) +
                                  " " +
                                  c.created_at
                                    .replace("T", " ")
                                    .substring(8, 10) +
                                  "-" +
                                  c.created_at
                                    .replace("T", " ")
                                    .substring(5, 7) +
                                  "-" +
                                  c.created_at
                                    .replace("T", " ")
                                    .substring(0, 4)}
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
                          <div className="msg">
                            <a
                              onClick={() => handleClick(index)}
                              className="clickable text-muted"
                              role="button"
                            >
                              <i className="clickable fa fa-comments"></i>
                              {c.comments.length > 0 ? (
                                <>
                                  {t(
                                    "jobs.timeline.timelineCommentDescription.view"
                                  )}
                                </>
                              ) : (
                                <>
                                  {t(
                                    "jobs.timeline.timelineCommentDescription.add"
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
                                            placeholder="Lisa kommentaar..."
                                            style={{
                                              fontSize: `${
                                                settingsSelector.FontSize ===
                                                "Large"
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
                                              settingsSelector.FontSize ===
                                              "Large"
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
                  {timelinedata && nextjobsRow < timelinedata.length && (
                    <Button
                      className="mt-4"
                      color={"primary"}
                      onClick={handlejobDataPerRow}
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
            </>
          }
        </Card>
      </div>
    </>
  );
};

export default JobsActivity;
