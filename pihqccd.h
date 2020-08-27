  /**********************************************************************************************************
   **   File: pihqccd.h                                                                                  **
   **   Author: Gord Tulloch (gord.tulloch@gmail.com)                                                      **
   **   Version 0.0                                                                                        **
   **********************************************************************************************************/
#pragma once

#include "indiccd.h"

class PihqCCD : public INDI::CCD
{
  public:
    PihqCCD() = default;

  protected:
    // General device functions
    bool Connect();
    bool Disconnect();
    const char *getDefaultName();
    bool initProperties();
    bool updateProperties();

    // CCD specific functions
    bool StartExposure(float duration);
    bool AbortExposure();
    int SetTemperature(double temperature);
    void TimerHit();

  private:
    // Utility functions
    float CalcTimeLeft();
    void setupParams();
    void grabImage();

    // Are we exposing?
    bool InExposure { false };
    // Struct to keep timing
    struct timeval ExpStart { 0, 0 };

    float ExposureRequest { 0 };
    float TemperatureRequest { 0 };
};
