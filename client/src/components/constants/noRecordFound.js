import React from "react";
import { Button, Card, CardBody, Col, Row } from "reactstrap";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

export const NoListFound = () => {
  const { t } = useTranslation();
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>{t("recordNotFound.noListFound")}</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const NoRecordFound = () => {
  const { t } = useTranslation();
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>{t("recordNotFound.noRecordFound")}</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const NoRecordFoundDynamic = ({ message }) => {
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>{message}</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const NoJobFound = () => {
  const { t } = useTranslation();
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>{t("recordNotFound.noJobFound")}</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const CustomErrors = ({ message }) => {
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <Card>
          <CardBody>{message}</CardBody>
        </Card>
      </Col>
    </Row>
  );
};

export const NoMatchFound = () => {
  const { t } = useTranslation();
  const settingsSelector = useSelector((state) => state.settings);
  return (
    <Row className={"w-100 ml-1 mr-1"}>
      <Col sm={12} md={12}>
        <table className="table mb-0" id="myTable">
          <Button
            className={
              "w-100 bg-none border-0 text-dark hover-overlay apply-to-job-button text-left"
            }
            style={{
              fontSize: `${
                settingsSelector.FontSize === "Large"
                  ? "large"
                  : settingsSelector.FontSize === "Extra Large"
                  ? "x-large"
                  : "14px"
              }`,
              cursor: "context-menu",
            }}
          >
            {t("recordNotFound.noMatchFound")}
          </Button>
        </table>
      </Col>
    </Row>
  );
};
