import React, { useState } from "react";
import AddPersonalData from "./AddPersonalData";
import JobHistory from "./AddJobHistory";
import Education from "./AddEducation";
import AdditionalCourses from "./AdditionalCourses";
import Language from "./AddLanguage";
import Files from "./AddFiles";
import { Button, Card, CardHeader, Container, Row } from "reactstrap";
import { useHistory } from "react-router-dom";
import AddCandidatePersonalInfoError from "./AddCandidatePersonalInfoError";
import Agreements from "./Agreements";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

const ManuallyAddedCandidate = ({ cvData, viewPdfFile, cvFile }) => {
  const history = useHistory();

  const { t } = useTranslation();

  const [tabsId, setTabsId] = useState("1");
  const [candidateID, setCandidateID] = useState("");
  const [candidateEmail, setCandidateEmail] = useState("");

  const setBookMart = (tabID) => {
    setTabsId(tabID);
  };

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <Container fluid={true}>
      <Button
        type={"button"}
        color={"primary"}
        className={"mt-2"}
        onClick={() => history.push("/hr-candidate")}
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
        {t("candidates.addNewCandidate.back")}
      </Button>
      <Card className="mt-2">
        <CardHeader className="float-right">
          <Container>
            <Row className={"justify-content-around"}>
              <div
                className={tabsId === "1" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("1")}
              >
                {t("candidates.bookmarks.personalInformationBookmark")}
              </div>
              <div
                className={tabsId === "2" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("2")}
              >
                {t("candidates.bookmarks.jobHistoryBookmark")}
              </div>
              <div
                className={tabsId === "3" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("3")}
              >
                {t("candidates.bookmarks.educationBookmark")}
              </div>
              <div
                className={tabsId === "4" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("4")}
              >
                {t("candidates.bookmarks.additionalCoursesBookmark")}
              </div>
              <div
                className={tabsId === "5" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("5")}
              >
                {t("candidates.bookmarks.languagesBookmark")}
              </div>
              <div
                className={tabsId === "6" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("6")}
              >
                {t("candidates.bookmarks.filesBookmark")}
              </div>
              <div
                className={tabsId === "7" ? "active pointer" : "pointer"}
                onClick={() => setBookMart("7")}
              >
                {t("candidates.bookmarks.agreementBookmark")}
              </div>
            </Row>
          </Container>
        </CardHeader>
      </Card>
      {tabsId === "1" ? (
        <AddPersonalData
          setCandidateID={setCandidateID}
          cvData={cvData}
          viewPdfFile={viewPdfFile}
          setCandidateEmail={setCandidateEmail}
          setTabsId={setTabsId}
          cvFile={cvFile}
        />
      ) : tabsId === "2" && candidateID ? (
        <JobHistory
          candidateCvID={candidateID}
          cvData={cvData}
          viewPdfFile={viewPdfFile}
          setTabsId={setTabsId}
          cvFile={cvFile}
        />
      ) : tabsId === "3" && candidateID ? (
        <Education
          candidateCvID={candidateID}
          cvData={cvData}
          viewPdfFile={viewPdfFile}
          setTabsId={setTabsId}
          cvFile={cvFile}
        />
      ) : tabsId === "4" && candidateID ? (
        <AdditionalCourses
          candidateCvID={candidateID}
          cvData={cvData}
          viewPdfFile={viewPdfFile}
          setTabsId={setTabsId}
          cvFile={cvFile}
        />
      ) : tabsId === "5" && candidateID ? (
        <Language
          candidateCvID={candidateID}
          cvData={cvData}
          viewPdfFile={viewPdfFile}
          setTabsId={setTabsId}
          cvFile={cvFile}
        />
      ) : tabsId === "6" && candidateID ? (
        <Files
          candidateCvID={candidateID}
          viewPdfFile={viewPdfFile}
          setTabsId={setTabsId}
        />
      ) : tabsId === "7" && candidateID ? (
        <Agreements
          candidateCvID={candidateID}
          candidateEmail={candidateEmail}
          viewPdfFile={viewPdfFile}
        />
      ) : (
        <AddCandidatePersonalInfoError />
      )}
    </Container>
  );
};

export default ManuallyAddedCandidate;
