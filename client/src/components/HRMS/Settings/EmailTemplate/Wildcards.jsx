import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  Button,
  Card,
  CardBody,
  CardHeader,
  CardTitle,
  Col,
  Container,
  Form,
  Input,
  Row,
} from "reactstrap";
import { getWildcards } from "../../../../redux/actions/emailTemplateActions";
import { DataLoader } from "../../../constants/loaders";
import { useTranslation } from "react-i18next";

const Wildcards = ({ activeField, body, setBody, disabledCards }) => {
  const dispatch = useDispatch();

  const { t } = useTranslation();

  const [wildcardsData, setWildcardsData] = useState([]);
  const [wildCardSearchTerm, setWildCardSearchTerm] = useState("");

  const settingsSelector = useSelector((state) => state.settings);

  const fetchWildcards = async () => {
    try {
      const response = await dispatch(getWildcards());
      const wildCardResponse = response.payload.data;
      if (wildCardResponse.length > 0) {
        setWildcardsData(wildCardResponse);
      }
    } catch (error) {
      throw error;
    }
  };

  useEffect(() => {
    fetchWildcards();
  }, []);

  const handleTags = (hashtags) => {
    if (activeField !== null) {
      let inputEl = document.getElementById(activeField);
      if (inputEl === null && activeField === "cke_1") {
        setBody(body + " " + hashtags);
      } else if (
        inputEl.id == "title" ||
        inputEl.id == "template_key" ||
        inputEl.id == "to"
      ) {
        inputEl.value = inputEl.value + " " + hashtags;
      }
      // setActiveField(null);
    }
  };

  return (
    <Col md={4}>
      <Container fluid={true}>
        <Card>
          <CardHeader>
            <CardTitle
              className={"font-weight-bold"}
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
              {t("settings.emailTemplates.wildCards.heading")}
            </CardTitle>
          </CardHeader>
          <CardBody>
            <div className={"p-3"} style={{ backgroundColor: "#EBECF0" }}>
              <h6
                className={"font-weight-bold"}
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
                {t("settings.emailTemplates.wildCards.subHeading")}
              </h6>
              <p className={"mt-2"}>
                {t("settings.emailTemplates.wildCards.paragraph1")}
              </p>
              <p className={"mt-2"}>
                {t("settings.emailTemplates.wildCards.paragraph2")}
              </p>
            </div>
            <div className={"mt-3"}>
              <form>
                <div className={"input-group"}>
                  <Input
                    type={"text"}
                    name={"s"}
                    placeholder={"Otsi..."}
                    style={{
                      backgroundColor: "#EBECF0",
                      fontSize: `${
                        settingsSelector.FontSize === "Large"
                          ? "large"
                          : settingsSelector.FontSize === "Extra Large"
                          ? "x-large"
                          : "14px"
                      }`,
                    }}
                    onChange={(e) => setWildCardSearchTerm(e.target.value)}
                  />
                </div>
              </form>
            </div>
            <div
              className={"mt-3"}
              style={{ overflow: "auto", height: "500px" }}
            >
              <Form>
                {wildcardsData ? (
                  wildcardsData
                    .filter((e) => {
                      if (wildCardSearchTerm === "") {
                        return e;
                      } else if (
                        e.wildcard_key
                          .toLowerCase()
                          .includes(wildCardSearchTerm.toLowerCase())
                      ) {
                        return e;
                      }
                    })
                    .map((items, index) => (
                      <div key={index}>
                        <Button
                          type={"button"}
                          className={"w-100 mt-2 p-2 border text-center"}
                          style={{
                            backgroundColor: `${
                              body?.includes(items.wildcard_key)
                                ? "#007bff"
                                : "#fff"
                            }`,
                            color: "#000",
                            fontSize: `${
                              settingsSelector.FontSize === "Large"
                                ? "large"
                                : settingsSelector.FontSize === "Extra Large"
                                ? "x-large"
                                : "14px"
                            }`,
                          }}
                          disabled={disabledCards}
                          onClick={() => handleTags(items.wildcard_key)}
                        >
                          <h6
                            className={"m-0"}
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
                            {items.wildcard_key}
                          </h6>
                          <span className={"font-weight-bold"}>
                            {items.wildcard_value}
                          </span>
                        </Button>
                      </div>
                    ))
                ) : (
                  <DataLoader />
                )}
              </Form>
            </div>
          </CardBody>
        </Card>
      </Container>
    </Col>
  );
};

export default Wildcards;
