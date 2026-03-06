"use client"

import { useState } from "react"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { 
  CalendarCheck, 
  Users, 
  TrendingUp, 
  Clock,
  Download,
  BarChart3,
} from "lucide-react"

// Mock data for reports
const dailyStats = [
  { day: "Mon", reservations: 18, guests: 52 },
  { day: "Tue", reservations: 22, guests: 68 },
  { day: "Wed", reservations: 25, guests: 78 },
  { day: "Thu", reservations: 28, guests: 85 },
  { day: "Fri", reservations: 42, guests: 128 },
  { day: "Sat", reservations: 48, guests: 152 },
  { day: "Sun", reservations: 32, guests: 98 },
]

const peakHours = [
  { hour: "5:00 PM", reservations: 8 },
  { hour: "5:30 PM", reservations: 12 },
  { hour: "6:00 PM", reservations: 18 },
  { hour: "6:30 PM", reservations: 22 },
  { hour: "7:00 PM", reservations: 32 },
  { hour: "7:30 PM", reservations: 38 },
  { hour: "8:00 PM", reservations: 28 },
  { hour: "8:30 PM", reservations: 18 },
  { hour: "9:00 PM", reservations: 12 },
  { hour: "9:30 PM", reservations: 6 },
]

const zoneBreakdown = [
  { zone: "Main Dining Room", reservations: 145, percentage: 55 },
  { zone: "The Patio", reservations: 72, percentage: 27 },
  { zone: "The Bar", reservations: 48, percentage: 18 },
]

const monthlyTrends = [
  { month: "Sep", reservations: 480 },
  { month: "Oct", reservations: 520 },
  { month: "Nov", reservations: 610 },
  { month: "Dec", reservations: 750 },
  { month: "Jan", reservations: 580 },
  { month: "Feb", reservations: 620 },
  { month: "Mar", reservations: 680 },
]

export default function AdminReportsPage() {
  const [timeRange, setTimeRange] = useState("week")

  const maxReservations = Math.max(...dailyStats.map(d => d.reservations))
  const maxHourlyReservations = Math.max(...peakHours.map(h => h.reservations))
  const maxMonthlyReservations = Math.max(...monthlyTrends.map(m => m.reservations))

  return (
    <div className="p-6 lg:p-8 space-y-8">
      {/* Header */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 className="font-serif text-2xl md:text-3xl text-foreground">Reports</h1>
          <p className="text-muted-foreground mt-1">
            Analytics and insights for your restaurant
          </p>
        </div>
        <div className="flex items-center gap-4">
          <Select value={timeRange} onValueChange={setTimeRange}>
            <SelectTrigger className="w-40 bg-background">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="week">This Week</SelectItem>
              <SelectItem value="month">This Month</SelectItem>
              <SelectItem value="quarter">This Quarter</SelectItem>
              <SelectItem value="year">This Year</SelectItem>
            </SelectContent>
          </Select>
          <Button variant="outline">
            <Download className="h-4 w-4 mr-2" />
            Export
          </Button>
        </div>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Total Reservations</p>
                <p className="text-3xl font-semibold text-foreground">265</p>
                <p className="text-xs text-green-600">+12% vs last week</p>
              </div>
              <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <CalendarCheck className="h-5 w-5 text-primary" />
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Total Guests</p>
                <p className="text-3xl font-semibold text-foreground">661</p>
                <p className="text-xs text-green-600">+8% vs last week</p>
              </div>
              <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <Users className="h-5 w-5 text-primary" />
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Avg. Party Size</p>
                <p className="text-3xl font-semibold text-foreground">2.5</p>
                <p className="text-xs text-muted-foreground">No change</p>
              </div>
              <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <TrendingUp className="h-5 w-5 text-primary" />
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent className="p-6">
            <div className="flex items-start justify-between">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground">Peak Hour</p>
                <p className="text-3xl font-semibold text-foreground">7:30</p>
                <p className="text-xs text-muted-foreground">PM, consistently</p>
              </div>
              <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <Clock className="h-5 w-5 text-primary" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Tabs for different reports */}
      <Tabs defaultValue="daily" className="space-y-6">
        <TabsList className="grid w-full max-w-lg grid-cols-3">
          <TabsTrigger value="daily">Daily</TabsTrigger>
          <TabsTrigger value="hourly">Peak Hours</TabsTrigger>
          <TabsTrigger value="trends">Trends</TabsTrigger>
        </TabsList>

        {/* Daily Report */}
        <TabsContent value="daily" className="space-y-6">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card className="lg:col-span-2">
              <CardHeader>
                <CardTitle className="font-serif text-lg">Daily Reservations</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {dailyStats.map((day) => (
                    <div key={day.day} className="flex items-center gap-4">
                      <span className="w-10 text-sm font-medium text-muted-foreground">{day.day}</span>
                      <div className="flex-1 h-8 bg-muted rounded-sm overflow-hidden">
                        <div
                          className="h-full bg-primary rounded-sm flex items-center justify-end px-2"
                          style={{ width: `${(day.reservations / maxReservations) * 100}%` }}
                        >
                          <span className="text-xs text-primary-foreground font-medium">
                            {day.reservations}
                          </span>
                        </div>
                      </div>
                      <span className="w-16 text-sm text-muted-foreground text-right">
                        {day.guests} guests
                      </span>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="font-serif text-lg">Zone Breakdown</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {zoneBreakdown.map((zone) => (
                  <div key={zone.zone} className="space-y-2">
                    <div className="flex items-center justify-between">
                      <span className="text-sm font-medium text-foreground">{zone.zone}</span>
                      <span className="text-sm text-muted-foreground">{zone.percentage}%</span>
                    </div>
                    <div className="h-2 bg-muted rounded-full overflow-hidden">
                      <div
                        className="h-full bg-primary rounded-full"
                        style={{ width: `${zone.percentage}%` }}
                      />
                    </div>
                    <p className="text-xs text-muted-foreground">{zone.reservations} reservations</p>
                  </div>
                ))}
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Peak Hours Report */}
        <TabsContent value="hourly" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg">Reservations by Hour</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-end justify-between h-64 gap-2">
                {peakHours.map((hour) => (
                  <div key={hour.hour} className="flex-1 flex flex-col items-center gap-2">
                    <div className="w-full flex flex-col items-center">
                      <span className="text-xs font-medium text-foreground mb-1">{hour.reservations}</span>
                      <div
                        className="w-full bg-primary rounded-t-sm transition-all"
                        style={{ 
                          height: `${(hour.reservations / maxHourlyReservations) * 180}px`,
                          minHeight: "8px"
                        }}
                      />
                    </div>
                    <span className="text-xs text-muted-foreground -rotate-45 origin-top-left translate-x-2">
                      {hour.hour.replace(" PM", "")}
                    </span>
                  </div>
                ))}
              </div>
              <p className="text-center text-sm text-muted-foreground mt-8">
                Peak dining hours are between 7:00 PM and 8:00 PM
              </p>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Monthly Trends */}
        <TabsContent value="trends" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="font-serif text-lg">Monthly Reservation Trends</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-end justify-between h-64 gap-4">
                {monthlyTrends.map((month) => (
                  <div key={month.month} className="flex-1 flex flex-col items-center gap-2">
                    <span className="text-xs font-medium text-foreground">{month.reservations}</span>
                    <div
                      className="w-full bg-primary rounded-t-sm transition-all"
                      style={{ 
                        height: `${(month.reservations / maxMonthlyReservations) * 200}px`,
                        minHeight: "8px"
                      }}
                    />
                    <span className="text-sm text-muted-foreground font-medium">
                      {month.month}
                    </span>
                  </div>
                ))}
              </div>
              <div className="mt-8 p-4 bg-muted rounded-sm">
                <div className="flex items-center gap-2 mb-2">
                  <TrendingUp className="h-4 w-4 text-green-600" />
                  <span className="text-sm font-medium text-foreground">Overall Growth</span>
                </div>
                <p className="text-sm text-muted-foreground">
                  Reservations have increased by 42% over the past 6 months, with December showing the highest volume due to holiday season.
                </p>
              </div>
            </CardContent>
          </Card>

          {/* Additional Insights */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card>
              <CardHeader>
                <CardTitle className="font-serif text-lg">Top Performing Days</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-3">
                  <div className="flex items-center justify-between p-3 bg-muted rounded-sm">
                    <span className="font-medium text-foreground">Saturday</span>
                    <span className="text-sm text-muted-foreground">~48 reservations/day</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-muted rounded-sm">
                    <span className="font-medium text-foreground">Friday</span>
                    <span className="text-sm text-muted-foreground">~42 reservations/day</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-muted rounded-sm">
                    <span className="font-medium text-foreground">Sunday</span>
                    <span className="text-sm text-muted-foreground">~32 reservations/day</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="font-serif text-lg">Cancellation Rate</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="text-center py-4">
                  <p className="text-4xl font-semibold text-foreground">4.2%</p>
                  <p className="text-sm text-muted-foreground mt-2">Average cancellation rate</p>
                  <p className="text-xs text-green-600 mt-1">Below industry average of 8%</p>
                </div>
                <div className="mt-4 p-3 bg-accent/50 rounded-sm">
                  <p className="text-xs text-muted-foreground">
                    Most cancellations occur within 24 hours of the reservation time. Consider implementing a confirmation reminder system.
                  </p>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>
      </Tabs>
    </div>
  )
}
