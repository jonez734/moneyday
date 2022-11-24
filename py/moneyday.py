import datetime
import calendar

import ttyio5 as ttyio
import bbsengine5 as bbsengine

def work(day:int, which:int, month:int, year:int) -> datetime:
  date = datetime.date(year, month, 1)
  offset = (day-date.isoweekday()) % 7
  return datetime.date(year, month, 1+offset+(7*(which-1)))

def adjust(year, month):
  if month > 12:
    year += 1
    month -= 12
  elif month < 1:
    year -= 1
    month += 12
  return datetime.date(year, month, 1)

if __name__ == "__main__":
  year = ttyio.inputinteger("year: ")
  month = ttyio.inputinteger("month: ")
  which = ttyio.inputinteger("which day (0=sun): ", 3)
#  window = ttyio.inputinteger("window: ", 1)
  
  cur = work(3, 2, 11, 2022)
  nxt = work(3, 2, 12, 2022)
  print(nxt - cur)
